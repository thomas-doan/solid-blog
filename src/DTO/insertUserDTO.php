<?php

namespace App\DTO;
use App\Core\DataTransferObjectManager\DTOInterface;

class insertUserDTO implements DTOInterface {

    /**
     * @isMail
     * @isUnique
     */
    public string $email = "john@gmail.fr";

    /**
     * @isPassword
     */
    public string $password = "Toto1234_";

    /**
     * @minLength : 2
     * @maxLength : 50
     */
    public string $firstname = "John";

    /**
     * @minLength : 2
     * @maxLength : 50
     */
    public string $lastname = "Doe";

}