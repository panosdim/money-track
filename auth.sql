CREATE TABLE `auth_tokens`(
    `id` INTEGER(32) NOT NULL AUTO_INCREMENT,
    `series` VARCHAR(255) NOT NULL,
    `hashedToken` VARCHAR(255) NOT NULL,
    `username` VARCHAR(255) NOT NULL,
    PRIMARY KEY(`id`)
)