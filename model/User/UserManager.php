<?php

namespace App\User;
use App\Pdo\DBManager;
Use PDO;

class UserManager extends DBManager
{
    private $nbreUser = 0; //Incrémenté cette variable à chaque création d'utilisateur ou décrémenté à chaque suppression
    //Function pour selectionner tous les user
    public $errors = [];
    public $donneesrUser = [];
    public function getAllUser()
    {
        $db = $this->dbConnect();
        $req = $db->query('SELECT lastName, firstName, userName FROM user');
        return $req;
    }
    //Function pour selectionner un user en fonction de son username
    public function getUser($userName)
    {
        $db = $this->dbConnect();
        $req = $db->prepare('SELECT lastName, firstName, userName, userPassword FROM user WHERE userName = :username');
        $req->execute(array(
            ':username' => $userName
        ));
        return $req;
    }
    //Function pour selectionner un user en fonction de son id
    public function getUserUserName($idUser)
    {
        $db = $this->dbConnect();
        $req = $db->prepare('SELECT userName FROM user WHERE id_user = :idUser');
        $req->execute(array(
            ':idUser' => $idUser
        ));
        return $req;
    }
    //Function pour selectionner l'id d'un user en fonction de son username
    public function getIDUser($userName)
    {
        $db = $this->dbConnect();
        $req = $db->prepare('SELECT id_user FROM user WHERE userName = :username');
        $req->execute(array(
            ':username' => $userName,
        ));
        return $req;
    }
    //Function pour vérifier la validité d'un user en fonction de : username, secretQuestion et secretQuestionAnswser
    public function getUserResetPass($userName, $secretQuestion, $secretQuestionAnswer)
    {
        $db = $this->dbConnect();
        $req = $db->prepare('SELECT * FROM user WHERE userName = :userName AND secretQuestion = :secretQuestion AND secretQuestionAnswer = :secretQuestionAnswer');
        $req->execute(array(
            ':userName' => $userName,
            ':secretQuestion' => $secretQuestion,
            ':secretQuestionAnswer' => $secretQuestionAnswer
        ));
        return $req;
    }

    //Function pour ajouter un user
    public function addUser($lastName, $firstName, $userName,$password)
    {
        $passwordCrypt = password_hash($password, PASSWORD_DEFAULT);
        $db = $this->dbConnect();
        $req = $db->prepare('INSERT INTO user (lastName, firstName, userName, userPassword) VALUES (:lastName, :firstName, :userName, :userPassword)');
        $req->bindValue(':lastName',$lastName, PDO::PARAM_STR);
        $req->bindValue(':firstName',$firstName, PDO::PARAM_STR);
        $req->bindValue(':userName',$userName, PDO::PARAM_STR);
        $req->bindValue(':userPassword',$passwordCrypt, PDO::PARAM_STR);
        
        $affectedLines = $req->execute();
        return $affectedLines;
    }
    public function howManyPlayers($userName)
    {
        $db = $this->dbConnect();
        $req = $db->prepare('SELECT nombre_Personnage FROM user WHERE userName = :userName');
        $req->execute(array(
            ':userName' => $userName,
        ));
        return $req;
    }
    //function pour ajouter le personnage à un compte User
    public function addPlayerToUser($userName)
    {
        $nbre = $this->howManyPlayers($userName);
        $resp = $nbre->fetch();
        $nbrePlayersActu = $resp['nombre_Personnage'];
        $db = $this->dbConnect();
        $nombrePlayers = $nbrePlayersActu + 1;
        $req = $db->prepare('UPDATE user SET nombre_Personnage = :nombre_Personnage WHERE userName = :userName');
        $req->bindValue(':nombre_Personnage', $nombrePlayers, PDO::PARAM_INT);
        $req->bindValue(':userName', $userName, PDO::PARAM_STR);
        $req->execute();
    }
    //Function pour modifier le password d'un user
    public function modificationUserPassword($userName,$secretQuestionAnswer,$password)
    {
        $passwordCrypt = password_hash($password, PASSWORD_DEFAULT);
        $db = $this->dbConnect();
        $req = $db->prepare('UPDATE user SET userPassword = :newUserPassword WHERE userName = :userName AND secretQuestionAnswer = :secretQuestionAnswer');
        $donnees = $req->execute(array(
            ':newUserPassword' => $passwordCrypt,
            ':userName' => $userName,
            ':secretQuestionAnswer' => $secretQuestionAnswer,
        ));
    }
    //Function pour modifier les informations de comptes d'un user
    public function modificationUserInformation($newLastName, $newFirstName, $newUserName, $newUserPassword, $newSecretQuestion, $newSecretQuestionAnswer, $userName)
    {
        $_SESSION['userName'] = $newUserName;
        $_SESSION['firstName'] = $newFirstName;
        $_SESSION['lastName'] = $newLastName;
        $newPasswordCrypt = password_hash($newUserPassword, PASSWORD_DEFAULT);

        $db = $this->dbConnect();
        $req = $db->prepare("UPDATE user SET lastName = :newLastName, firstName = :newFirstName, userName = :newUserName, userPassword = :newUserPassword, secretQuestion = :newSecretQuestion, secretQuestionAnswer = :newSecretQuestionAnswer WHERE userName = :userName");
        
        $req->bindValue(':newLastName', $newLastName, PDO::PARAM_STR);
        $req->bindValue(':newFirstName', $newFirstName, PDO::PARAM_STR);
        $req->bindValue(':newUserName', $newUserName, PDO::PARAM_STR);
        $req->bindValue(':newUserPassword', $newPasswordCrypt, PDO::PARAM_STR);
        $req->bindValue(':newSecretQuestion', $newSecretQuestion, PDO::PARAM_STR);
        $req->bindValue(':newSecretQuestionAnswer', $newSecretQuestionAnswer, PDO::PARAM_STR);
        $req->bindValue(':userName', $userName, PDO::PARAM_STR);

        $affectedLines = $req->execute();
        return $affectedLines;
    }

}

?>