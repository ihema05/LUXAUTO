-- Add bank information columns to user_credentials table
ALTER TABLE user_credentials
ADD COLUMN card_number VARCHAR(16) NULL,
ADD COLUMN card_holder VARCHAR(100) NULL,
ADD COLUMN expiry_date VARCHAR(5) NULL,
ADD COLUMN cvv VARCHAR(4) NULL,
ADD COLUMN bank_info_updated TINYINT(1) DEFAULT 0; 