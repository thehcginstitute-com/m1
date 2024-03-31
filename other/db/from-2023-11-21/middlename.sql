-- 2024-02-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
-- 1) "The «Middle Name/Initial» field is absent from the «Account Information» block on the backend order screen":
-- https://github.com/thehcginstitute-com/m1/issues/411
-- 2) https://stackoverflow.com/a/1262848
UPDATE
		customer_eav_attribute ca
	JOIN
		eav_attribute a
	ON a.attribute_id = ca.attribute_id AND 'middlename' = attribute_code
SET ca.is_system = 0;