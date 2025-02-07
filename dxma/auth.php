<?php
if (!defined('DXMA_VERSION')) die();

function validUsername(string $user) {
    return preg_match('[^A-Za-z0-9 \'.,_-]', $user) === 0;
}

class AuthSystem {
    protected $model;
    protected $ctrl;
    public $ok;
    public $uid;
    public $uname;
    public $user;
    public $error;

    public function __construct($model, $ctrl) {
        $this->model = $model;
        $this->ctrl = $ctrl;
        session_start();
        if (empty($_SESSION['token'])) {
            $_SESSION['token'] = bin2hex(random_bytes(32));
        }
        $token = $_SESSION['token'];
        $now = time();
        $valid = isset($_SESSION['login_valid']) && $_SESSION['login_valid'];
        if ($valid) {
            $expires = $_SESSION['login_expires'];
            $valid = $valid && $now < $expires;
        }
        if ($valid) {
            $user = $this->model->getUserByIdLite($_SESSION['login_uid']);
            $valid = $valid && ($user !== NULL);
            if ($valid) {
                $uname = $user['username'];
                $this->user = $user;
            }
        }
        if ($valid) {
            $this->ok = true;
            $this->uid = $_SESSION['login_uid'];
            $this->uname = $uname;
        } else {
            $this->ok = false;
            $this->uid = null;
            $this->uname = null;
        }
    }

    private function fail(string $msg) {
        $this->error = $msg;
        return FALSE;
    }

    public function register(string $uname, string $upass, string $upassc, string $email) {
        $uname = trim($uname);
        $email = trim($email);
        if (empty($uname)) return $this->fail("user name cannot be empty");
        if (empty($upass)) return $this->fail("password cannot be empty");
        if (strlen($uname) > 32) return $this->fail("username is too long");
        if (!validUsername($uname)) return $this->fail("username contains invalid characters");
        if (strlen($upass) > 256) return $this->fail("password is too long");
        if ($upass !== $upassc) return $this->fail("passwords do not match");

        if (empty($email)) $email = NULL;

        $user = $this->model->getUserByName($uname);
        if (!is_null($user)) return $this->fail("user name is already taken");

        $passhash = password_hash($upass, PASSWORD_DEFAULT);
        $uid = $this->ctrl->createUser($uname, $passhash, $email);
        if ($uid === NULL)
            return $this->fail("invalid values");

        $now = time();
        $_SESSION['login_valid'] = TRUE;
        $_SESSION['login_expires'] = $now + (20 * 24 * 60 * 60);
        $_SESSION['login_uid'] = $uid;
        return TRUE;
    }

    public function forgot(string $uname) {
        $uname = trim($uname);
        if (empty($uname)) return $this->fail("user name cannot be empty");

        $user = $this->model->getUserByName($uname);
        if (is_null($user)) return TRUE;

        $email = $user["email"];
        if (empty($email)) return TRUE;

        if (!CAN_EMAIL) return $this->fail("Email is not enabled");

        $forgotcode = bin2hex(random_bytes(16));
        if (!$this->ctrl->setUpForgot($user["id"], $forgotcode))
            return fail($this->ctrl->error);

        $link = PUBLIC_URL . "forgot/?" . http_build_query([ "u" => $user["id"], "t" => $forgotcode ]);
        $msg = "Hi,\r\n\r\nyou are receiving this email because someone filled in\r\n";
        $msg .= "the 'forgot my password' form with your username over at the\r\n";
        $msg .= "Descent Mission Archive at " . PUBLIC_URL . "\r\n\r\n";
        $msg .= "If this is indeed the case, use the link below (expires in 24 hours):\r\n";
        $msg .= "<a href=\"$link\">$link</a>\r\n\r\n";
        $msg .= "If you did not fill in the form, please ignore thie message.\r\n\r\n";
        $msg .= "P.S. This is an automated email. Please do not reply to it.\r\n";
        mail($email, "Descent Mission Archive: Forgot password", $msg);
        return TRUE;
    }

    public function changePassword(string $uid, string $upass) {
        if (strlen($upass) > 256) return $this->fail("password is too long");
        return password_hash($upass, PASSWORD_DEFAULT);
    }

    public function login(string $uname, string $upass) {
        $user = $this->model->getUserByName($uname);
        if (is_null($user)) return FALSE;

        if (!password_verify($upass, $user['passhash'])) return FALSE;
        $uid = $user['id'];

        $now = time();
        $_SESSION['login_valid'] = TRUE;
        $_SESSION['login_expires'] = $now + (20 * 24 * 60 * 60);
        $_SESSION['login_uid'] = $uid;
        return TRUE;
    }

    public function logout() {
        session_destroy();
    }
}
?>
