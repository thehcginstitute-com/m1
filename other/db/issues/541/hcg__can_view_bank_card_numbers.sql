ALTER TABLE `admin_user`
ADD `hcg__can_view_bank_card_numbers` SMALLINT(6) NOT NULL DEFAULT '0'
COMMENT 'https://github.com/thehcginstitute-com/m1/issues/541'
AFTER `is_active`;

UPDATE admin_user
SET hcg__can_view_bank_card_numbers = 1
WHERE user_id IN (5, 51);