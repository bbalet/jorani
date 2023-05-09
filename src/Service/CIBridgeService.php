<?php
namespace App\Service;

use App\Entity\CiSession;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Bridge to some CodeIngiter features of interrest during the migration.
 * An example is the session database driver to store session infos
 * @license AGPL-3.0
 * @copyright Copyright (c) 2023 Benjamin BALET
 * 
 */
class CIBridgeService
{
    /**
     * CI Session Id (and not PHP session id)
     *
     * @var string|null
     */
    private ?string $sessionId;
    /**
     * Firstname of connected user
     *
     * @var string|null
     */
    private ?string $firstName;
    /**
     * Lastname of connected user
     *
     * @var string|null
     */
    private ?string $lastName;
    /**
     * Concat of first and last name
     *
     * @var string|null
     */
    private ?string $fullName;
    /**
     * Is the user connected to Jorani
     *
     * @var boolean
     */
    private bool $isLoggedIn;
    /**
     * Is the connected user a manager
     *
     * @var boolean
     */
    private bool $isManager;
    /**
     * Has the connected user the admin role
     *
     * @var boolean
     */
    private bool $isAdmin;
    /**
     * Has the connected user the HR role
     *
     * @var boolean
     */
    private bool $isHr;
    /**
     * Id of connected user in the database
     *
     * @var integer
     */
    private int $userId;
    /**
     * Manager Id of the connected user
     *
     * @var integer
     */
    private int $manager;
    /**
     * Language name of the connected user
     *
     * @var string
     */
    private string $language;
    /**
     * Language ISO code of the connected user
     *
     * @var string
     */
    private string $languageCode;

    /**
     * Constructor. Try to load data from a possible session stored in the database
     *
     * @param EntityManagerInterface $entityManager
     */
    function __construct(EntityManagerInterface $entityManager) {
        $this->isLoggedIn = false;
        $this->isManager = false;
        $this->isAdmin = false;
        $this->isHr = false;
        $this->language = 'english';
        $this->languageCode = 'en';
        if (array_key_exists('jorani_session', $_COOKIE)) {
            $this->sessionId = $_COOKIE['jorani_session'];
        }else {
            $this->sessionId = '';
        }
        if ($this->sessionId !== '') {
            $isLoggedIn = false;
            $session_id = $_COOKIE['jorani_session'];
            $session = $entityManager->getRepository(CiSession::class)->find($session_id);
            if (!is_null($session)) {
                session_start();
                $session_data = session_decode(stream_get_contents($session->getData()));
                if (array_key_exists('firstname', $_SESSION)) {
                    $this->firstName = $_SESSION['firstname'];
                    $this->lastName = $_SESSION['lastname'];
                    $this->fullName = $this->firstName . ' ' . $this->lastName;
                    $this->isLoggedIn = $_SESSION['logged_in'];
                    $this->isManager = $_SESSION['is_manager'];
                    $this->isAdmin = $_SESSION['is_admin'];
                    $this->isHr = $_SESSION['is_hr'];
                    $this->userId = $_SESSION['id'];
                    $this->manager = $_SESSION['manager'];
                    $this->languageCode = $_SESSION['language_code'];
                }
            } 
        }
    }

    /**
     * Check if the user is logged in, redirect to login page otherwise.
     * do not use the router of symfony, because it can be implemented into the legacy application.
     *
     * @return void
     */
    public function checkIfLoggedInOrRedirect()
    {
        if ($this->isLoggedIn == false) {
            header("Location: session/login");
            exit();
        }
    }

    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    public function isLoggedIn(): bool
    {
        return $this->isLoggedIn;
    }

    public function isManager(): bool
    {
        return $this->isManager;
    }

    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }

    public function isHr(): bool
    {
        return $this->isHr;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getManager(): ?int
    {
        return $this->manager;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }

}
