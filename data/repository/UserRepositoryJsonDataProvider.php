<?php

/**
 * Class UserRepositoryJsonDataProvider
 * Provides data access functionality for UserRepository using a JSON file as the data source.
 */
class UserRepositoryJsonDataProvider {
    /** @var string The file path of the JSON file to be used as the data source. */
    private $jsonFilePath;

    /**
     * UserRepositoryJsonDataProvider constructor.
     * @param string $jsonFilePath The file path of the JSON file to be used as the data source.
     */
    public function __construct(string $jsonFilePath) {
        $this->jsonFilePath = $jsonFilePath;
    }
    
    /**
     * Load users from the JSON file.
     * @return array An array of User objects representing the loaded users.
     */
    public function loadUsers(): array {
        $json = file_get_contents($this->jsonFilePath);
        $data = json_decode($json);

        $users = [];
        foreach ($data as $userData) {
            $users[] = User::fromJson($userData);
        }

        return $users;
    }

    /**
     * Save users to the JSON file.
     * @param array $users An array of User objects representing the users to be saved.
     */
    public function saveUsers($users) {
        $jsonUsers = [];
        foreach ($users as $user) {
            $jsonUsers[] = $user->toArray();
        }

        $json = json_encode($jsonUsers , JSON_PRETTY_PRINT);
        file_put_contents($this->jsonFilePath, $json);
    }
}

?>