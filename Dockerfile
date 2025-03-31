FROM php:latest-apache

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
