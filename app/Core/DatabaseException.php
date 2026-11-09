<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

/** La base de datos no está configurada o no respondió. */
final class DatabaseException extends RuntimeException
{
}
