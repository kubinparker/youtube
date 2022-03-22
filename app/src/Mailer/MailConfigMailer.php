<?php

namespace App\Mailer;

use Cake\Mailer\Mailer;

class MailConfigMailer extends Mailer
{
    public $_from = ['daotaodaihoc@hus.edu.vn' => 'Phong Dao Tao DHKHTN'];


    public function __construct($config = null)
    {
        parent::__construct($config);
        $this
            ->setFrom($this->_from)
            ->setEmailFormat('text');
    }


    public function user_info($user)
    {
        $this
            ->setTo($user->email)
            ->setSubject(sprintf('Welcome CAKEPHP 4'))
            ->setViewVars(['_' => $user])
            ->viewBuilder()
            ->setTemplate('user_mail');
    }
}
