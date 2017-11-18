<?php

namespace Dersam\Multitenant;

/**
 * Forces the model to use the current tenant database.
 *
 * Should only be used on Eloquent models.
 */
trait IsTenantModel
{
    protected $connection = 'tenant';
}