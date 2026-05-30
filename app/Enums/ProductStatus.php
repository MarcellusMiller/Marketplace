<?php

namespace App\Enums;

enum ProductStatus: string
{
    // Enums in php looks like a class but with a fixed set of possible values. They are useful for representing a value that can only be one of a few options, such as the status of a product.
    case Active = "active";
    case Inactive = "inactive";
    case OutOfStock = "out_of_stock";
    case Disabled = "disabled";
}
