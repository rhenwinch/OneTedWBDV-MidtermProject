<?php

/**
 * Interface for updating user information.
 */
interface UserUpdater {
    /**
     * Update user information based on given parameters.
     *
     * @param User $user User object to update.
     * @param string|null $email New email address, if any.
     * @param string|null $password New password, if any.
     * @param string|null $name New name, if any.
     * @param string|null $phoneNumber New phone number, if any.
     * @param string|null $profilePicture New profile picture, if any.
     *
     * @return User The updated user object.
     */
    public function updateUser(
        User $user,
        ?string $email = null,
        ?string $password = null,
        ?string $name = null,
        ?string $phoneNumber = null,
        ?string $profilePicture = null
    ): User;
}


?>