<?php
/**
 * 2015-12-06 It trims the ending «/».
 * @used-by df_module_name_by_path()
 * @used-by \Df\Qa\Failure\Error::preface()
 * @used-by \Df\Qa\Trace\Frame::file()
 */
function df_path_relative(string $p):string {return df_trim_text_left(
	df_trim_ds_left(df_path_n($p)), df_trim_ds_left(df_path_n(BP . '/'))
);}