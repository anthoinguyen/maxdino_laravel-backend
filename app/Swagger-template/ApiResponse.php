<?php
namespace SwaggerIO;
/**
 * @SWG\Definition(type="object")
 */
class ApiResponse
{
    /**
     * @SWG\Property(format="int32")
     * @var bool
     */
    public $error;
    /**
     * @SWG\Property
     * @var string
     */
    public $data;
    /**
     * @SWG\Property
     * @var string
     */
    public $errors;
}