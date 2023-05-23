<?php
require_once __DIR__ . '/../model/User.php';
require_once 'UserRepositoryJsonDataProvider.php';

/**
 * Class UserRepository
 * Represents a repository for managing users, providing functionalities such as adding, updating, and deleting users.
 */
class UserRepository {
    /** @var UserRepositoryJsonDataProvider The data provider for UserRepository. */
    private $jsonDataProvider;
    /** @var array An array of User objects representing the users managed by UserRepository. */
    private $users;

    /**
     * UserRepository constructor.
     * @param string $jsonFilePath The file path to the JSON file containing user data.
     */
    public function __construct($jsonFilePath) {
        $this->jsonDataProvider = new UserRepositoryJsonDataProvider($jsonFilePath);
        $this->users = $this->jsonDataProvider->loadUsers();
    }

    /**
     * Obtain all users from database
     *
     * @return array Arraylist of users
     */
    public function getAllUsers(): array {
        return $this->users;
    }

    /**
     * Create a new user and add it to the repository.
     *
     * @param User $user The user object to be created and added.
     *
     * @return bool True if the user was added successfully, false if a user with the same userId already exists.
     */
    public function createUser(User $user): bool {
        // Check if user already exists
        if ($this->getUserByEmail($user->getEmail()) !== null) {
            return false; // User already exists, return false
        }

        // Add user to the users array
        $this->users[] = new User(
            $user->getEmail(),
            $user->getPassword(),
            count($this->users) + 1,
            $user->getName()
        );

        // Save users to JSON file
        $this->saveChanges();

        return true; // User added successfully, return true
    }

    /**
     * Update a user.
     * @param User $user The user to be updated.
     */
    public function updateUser(User $user) {
        // Find the index of the user in the users array
        $index = $this->getUserIndexById($user->getUserId());

        if ($index !== -1) {
            // Update the user object in the users array
            $this->users[$index] = $user;
            $this->saveChanges();
        }
    }

    /**
     * Delete a user.
     * @param User $user The user to be deleted.
     */
    public function deleteUser(User $user) {
        // Find the index of the user in the users array
        $index = $this->getUserIndexById($user->getUserId());

        if ($index !== -1) {
            // Remove the user object from the users array
            array_splice($this->users, $index, 1);
            $this->saveChanges();
        }
    }

    /**
     * Find a user by user ID.
     * @param int $userId The user ID to search for.
     * @return User|null The User object if found, or null if not found.
     */
    public function getUserById($userId): ?User {
        foreach ($this->users as $user) {
            if ($user->getUserId() === $userId) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Find a user by email.
     * @param string $email The email to search for.
     * @return User|null The User object if found, or null if not found.
     */
    public function getUserByEmail($email): ?User {
        foreach ($this->users as $user) {
            if ($user->getEmail() === $email) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Check if a given user exists in the repository.
     *
     * @param User $user The user to check for existence.
     *
     * @return bool True if the user exists, false otherwise.
     */
    public function userExists(User $user): bool {
        // Check if a given user exists in the repository
        $email = $user->getEmail();
        $user_ = $this->getUserByEmail($email);
        if($user_ === null)
            return false;
        
        $index = $this->getUserIndexById($user_->getUserId());
        return isset($this->users[$index]) && $this->users[$index]->getPassword() === $user->getPassword();
    }


    /**
     * Find the index of a user by user ID.
     * @param int $userId The user ID to search for.
     * @return int The index of the user in the users array, or -1 if not found.
     */
    private function getUserIndexById($userId): int {
        for ($i = 0; $i < count($this->users); $i++) {
            if ($this->users[$i]->getUserId() === $userId) {
                return $i;
            }
        }

        return -1;
    }

    /**
     * Save changes to the JSON data provider.
     */
    private function saveChanges() {
        $this->jsonDataProvider->saveUsers($this->users);
    }
}
