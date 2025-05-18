<?php

namespace App\Enums;

enum ProductCategory: string
{
    case FOOD_AND_BEVERAGES = 'food_and_beverages';
    case ELECTRONICS = 'electronics';
    case CLOTHING_AND_ACCESSORIES = 'clothing_and_accessories';
    case HOME_AND_FURNITURE = 'home_and_furniture';
    case HEALTH_AND_BEAUTY = 'health_and_beauty';
    case SPORTS_AND_LEISURE = 'sports_and_leisure';
    case BOOKS_AND_STATIONERY = 'books_and_stationery';
    case AUTOMOTIVE = 'automotive';

    // for user interface
    public function label(): string
    {
        return match ($this) {
            self::FOOD_AND_BEVERAGES => 'Food and Beverages',
            self::ELECTRONICS => 'Electronics',
            self::CLOTHING_AND_ACCESSORIES => 'Clothing and Accessories',
            self::HOME_AND_FURNITURE => 'Home and Furniture',
            self::HEALTH_AND_BEAUTY => 'Health and Beauty',
            self::SPORTS_AND_LEISURE => 'Sports and Leisure',
            self::BOOKS_AND_STATIONERY => 'Books and Stationery',
            self::AUTOMOTIVE => 'Automotive',
        };
    }
}
