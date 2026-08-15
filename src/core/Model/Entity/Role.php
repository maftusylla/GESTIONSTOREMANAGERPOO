<?php
declare(strict_types=1);

enum Role: string
{
    case ADMIN = 'ADMIN';
    case VENTE = 'VENTE';
    case STOCK = 'STOCK';
    case INVENTAIRE = 'INVENTAIRE';
}