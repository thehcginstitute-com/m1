-- 2024-02-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
-- «Invalid backend model specified: catalog/product_attribute_backend_recurring»:
-- https://github.com/thehcginstitute-com/m1/issues/408
DELETE FROM eav_attribute WHERE attribute_code LIKE '%recurring%';