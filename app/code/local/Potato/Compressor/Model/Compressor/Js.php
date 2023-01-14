<?php

class Potato_Compressor_Model_Compressor_Js extends Potato_Compressor_Model_Compressor_Abstract
{
    const FILE_EXTENSION         = 'js';

    public function compression($content)
    {
        $jsCompressor = new Compressor_Minify_JSMin('');
        $content = $jsCompressor->minify($content);
        return $content;
    }

    public function makeDefer($response)
    {
        $body = $response->getBody();
        //remove cdata
        $body = preg_replace('#//[ ]*<!\[CDATA\[(.+?)\/\/[ ]*\]\]>#s', '$1', $body);
        if (Potato_Compressor_Helper_Config::getDeferMethod() == Potato_Compressor_Model_Source_Defer::ATTRIBUTE_VALUE) {
            $body = $this->_addDeferAttribute($body);
        }
        if (Potato_Compressor_Helper_Config::getDeferMethod() == Potato_Compressor_Model_Source_Defer::MOVE_TO_BODY_END_VALUE) {
            $body = $this->_moveScripts($body);
        }
        $response->setBody($body);
        return $this;
    }

    protected function _moveScripts($body)
    {
        //todo bug with fpc
        //$body = preg_replace("/<!--.*?-->/ms","", $body);

        //get all script tags
        preg_match_all('/<script\b[^>]*>(.*?)<\/script>/is', $body, $matches);
        if (empty($matches)) {
            return $body;
        }
        $resultBody = $body;
        $ifDirectiveData = $this->_getIfDirectiveData($body);

        foreach ($matches[0] as $line) {
            $scriptLine = $line;
            //check ignore
            preg_match('@po_cmp_ignore@', $scriptLine, $match);
            if (!empty($match)) {
                continue;
            }
            //ignore <script type="application/ld+json">
            preg_match('@application/ld.json@', $scriptLine, $match);
            if (!empty($match)) {
                continue;
            }
            //remove script from body
            $resultBody = str_replace($line, '', $resultBody);

            //issue if line like // ]> </script>
            $scriptLine = str_replace('</script>', "\n</script>", $scriptLine);

            //issue if line like ]--> <script>
            $scriptLine = str_replace('<script>', "\n<script>", $scriptLine);

            $content = $scriptLine;
            preg_match('/<script.*src=.*>/', $scriptLine, $match);
            if (empty($match)) {
                //compress content
                try {
                    $content = $this->compression($scriptLine);
                } catch (Exception $e) {
                    $content = $scriptLine;
                }
            }

            //check is if directive needed
            $linePosition = strpos($body, $line);
            foreach ($ifDirectiveData as $ifData) {
                if ($linePosition > $ifData['startPosition']
                    && $linePosition < $ifData['endPosition'])
                {
                    $content = $ifData['startString'] . $content
                        . $ifData['endString']
                    ;
                    break;
                }
            }
            //move script in the end of the body
            $resultBody = str_replace(
                '</body>', $content . '</body>', $resultBody
            );
        }
        return $resultBody;
    }

    protected function _addDeferAttribute($body)
    {
        //getall script tags
        preg_match_all('/<script\b[^>]*>(.*?)<\/script>/is', $body, $matches);

        //inline scripts
        $nonSrcScriptLines = array();
        if (empty($matches)) {
            return $this;
        }
        foreach ($matches[0] as $line) {
            $scriptLine = $line;
            //check ignore
            preg_match('@po_cmp_ignore@', $scriptLine, $match);
            if (!empty($match)) {
                continue;
            }
            //ignore <script type="application/ld+json">
            preg_match('@application/ld.json@', $scriptLine, $match);
            if (!empty($match)) {
                continue;
            }

            //issue if line like // ]> </script>
            $scriptLine = str_replace('</script>', "\n</script>", $scriptLine);

            //issue if line like ]--> <script>
            $scriptLine = str_replace('<script>', "\n<script>", $scriptLine);
            //issue if line like </script><script>
            $scriptLine = str_replace('</script><script ', "</script>\n<script ", $scriptLine);

            //check src attr
            preg_match('/<script.*src=.*>/', $scriptLine, $match);
            if (!empty($match)) {
                $scriptLine = str_replace('<script ','<script defer ', $scriptLine);
                $body = str_replace($line, $scriptLine, $body);
                continue;
            }
            preg_match('/<script[^>]*>(.*)<\/script>/is', $scriptLine, $match);
            if (empty($match)) {
                continue;
            }
            //remove script tag
            $scriptLine = preg_replace('#<script(.*?)>#is', '', $scriptLine);
            $scriptLine = preg_replace('#</script>#is', '', $scriptLine);

            $nonSrcScriptLines[] = $scriptLine;
            //remove script from body
            $body = str_replace($line, '', $body);
        }
        if (!empty($nonSrcScriptLines)) {
            $content = '';
            //merge content
            foreach ($nonSrcScriptLines as $scriptLine) {
                $content .= rtrim($scriptLine, ';') . ';';
            }
            //compress content
            $content = $this->compression($content);

            //prepare inline js file
            $fileName = md5($content) . '.js';
            $filePath = Mage::helper('po_compressor')->getRootCachePath() . DS . Mage::app()->getStore()->getId() . DS . 'js';
            if (!file_exists($filePath . DS . $fileName)) {
                file_put_contents($filePath . DS . $fileName, $content);
            }
            $baseMediaUrl = Mage::helper('po_compressor')->getRootCacheUrl() . '/' . Mage::app()->getStore()->getId() . '/' . 'js' . '/';
            $body = str_replace('</body>', '<script defer src="' . $baseMediaUrl . $fileName . '"></script></body>', $body);
        }
        return $body;
    }

    private function _getIfDirectiveData($body)
    {
        preg_match_all('/<!-{0,2}\[if[^>]*>/', $body, $ifDirectiveMatch, PREG_OFFSET_CAPTURE);
        preg_match_all('/<!\[endif\]-{0,2}>/', $body, $endifDirectiveMatch, PREG_OFFSET_CAPTURE);
        if (empty($ifDirectiveMatch)) {
            return array();
        }
        $data =array();
        foreach ($ifDirectiveMatch[0] as $key => $if) {
            $data[] = array(
                'startString' => $if[0],
                'endString' => $endifDirectiveMatch[0][$key][0],
                'startPosition' => $if[1],
                'endPosition' => $endifDirectiveMatch[0][$key][1]
            );
        }
        return $data;
    }
}