<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'positions')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $login = null;

    // #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private int $role = 0;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->login;
    }

    /**
     * During the migration to symfony, we will use the bit mask system of Jorani
     * And then migrate to the JSON-based role system of Symfony
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        /*
            00000001 1  Admin
            00000100 8  HR Officier / Local HR Manager
            00001000 16 HR Manager
        */
        if (((int) $this->role & 1)) {
            $roles[] = 'ROLE_ADMIN';
        }
        if (((int) $this->role & 25)) {
            $roles[] = 'ROLE_HR';
        }

        //TODO Determine if the connected user is a manager or if he has any delegation
        /*$user->isManager = FALSE;
        if (count($this->getCollaboratorsOfManager($row->id)) > 0) {
            $user->isManager = TRUE;
        } else {
            $this->load->model('delegations_model');
            if ($this->delegations_model->hasDelegation($row->id))
                $user->isManager = TRUE;
        }*/

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
