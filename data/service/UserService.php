<?php
require_once __DIR__ . '/../model/User.php';
require_once __DIR__ . '/../repository/UserRepository.php';
require_once __DIR__ . '/../interfaces/UserUpdater.php';

/**
 * The UserService class handles the business logic for user-related functionality.
 */
class UserService implements UserUpdater {
    /**
     * The UserRepository instance.
     *
     * @var UserRepository
     */
    private $userRepository;

    /**
     * Create a new UserService instance.
     *
     * @param UserRepository|null $userRepository The UserRepository instance.
     */
    public function __construct(?UserRepository $userRepository = null) {
        if($userRepository == null) {
            $jsonFilePath = __DIR__ . '/../data/users.json'; // path to the JSON file containing user data
            $this->userRepository = new UserRepository($jsonFilePath);
            return;
        }

        $this->userRepository = $userRepository;
    }

    /**
     * Updates the specified user's information.
     *
     * @param User $user The user to update.
     * @param string|null $email The user's updated email address, or null to leave unchanged.
     * @param string|null $password The user's updated password, or null to leave unchanged.
     * @param string|null $name The user's updated name, or null to leave unchanged.
     * @param string|null $phoneNumber The user's updated phone number, or null to leave unchanged.
     * @param string|null $profilePicture The user's updated profile picture, or null to leave unchanged.
     * @return User The updated user object.
     */
    public function updateUser(
        User $user,
        ?string $email = null,
        ?string $password = null,
        ?string $name = null,
        ?string $phoneNumber = null,
        ?string $profilePicture = null
    ): User {
        $updatedEmail = $email ?? $user->getEmail();
        $updatedPassword = $password ?? $user->getPassword();
        $updatedName = $name ?? $user->getName();
        $updatedPhoneNumber = $phoneNumber ?? $user->getPhoneNumber();
        $updatedProfilePicture = $profilePicture ?? $user->getProfilePicture();
    
        $updatedUser = new User(
            $updatedEmail,
            $updatedPassword,
            $user->getUserId(),
            $updatedName,
            $updatedProfilePicture,
            $updatedPhoneNumber,
            $user->isMember(),
            $user->getBookingHistory(),
        );

        $this->userRepository->updateUser($updatedUser);
        return $updatedUser;
    }

    public function getUpdatedUser(
        int $userId
    ): ?User {
        return $this->userRepository->getUserById($userId);
    }
}
