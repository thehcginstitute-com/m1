-- 2024-02-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
-- «Unkown payment method with code "paypal_express_bml"»: https://github.com/thehcginstitute-com/m1/issues/410
DELETE FROM core_config_data WHERE path LIKE 'payment/paypal%';