/**
 * OpenMage
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available at https://opensource.org/license/afl-3-0-php
 *
 * @category    Mage
 * @package     js
 * @copyright   Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
/*@cc_on
// code only for IE7 when ExtJs overwrite "defer" function in PrototypeJs
(function(){
    var last = null;
    var ie7 = @if(@_jscript_version==5.7) 1 @end + 0;
    var ie8 = @if(@_jscript_version==5.8) 1 @end + 0;
    if (ie7 || ie8) {
        var eDefer = Function.prototype.defer;
        Function.prototype.defer = function() {
            // prevent throw stack overflow exception
            if (last !== this) {
                last = this;
                eDefer.apply(last, arguments);
            }
            return this;
        };
    }
})();
@*/
