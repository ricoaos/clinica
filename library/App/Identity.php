<?php

/**
 * Utilitário de dados do Cliente
 */
class App_Identity
{
    static public function get($atributo, $fonte = 'identity')
    {
        if ('id_organizacao' == $atributo) {
            return self::getOrganizacao();
        }

        if ('identity' == $fonte) {
            return isset(Zend_Auth::getInstance()->getIdentity()->{$atributo}) ? Zend_Auth::getInstance()->getIdentity()->{$atributo} : null;
        }

        return isset(Zend_Registry::get('config.cliente')->{$atributo}) ? Zend_Registry::get('config.cliente')->{$atributo} : null;
    }

    static function getOrganizacao()
    {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            return Zend_Auth::getInstance()->getIdentity()->organizacao->id_organizacao;
        }
        if (self::hasOrganizacao()) {
            return XGE_ORGANIZACAO;
        }
        
        //throw new Ev_Exception('Organização não encontrada.');
        $redirect = APPLICATION_PATH .'auth';
        header("location: $redirect");
    }

    static function hasOrganizacao()
    {
        return defined('XGE_ORGANIZACAO');
        return ('0' != XGE_ORGANIZACAO);
    }

    static function getLogin()
    {
        return Zend_Auth::getInstance()->getIdentity()->st_login;
    }

    static function getIdUsuario()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            return false;
        }

        return Zend_Auth::getInstance()->getIdentity()->id_usuario;
    }

    static function getGrupo()
    {
    	return Zend_Auth::getInstance()->getIdentity()->grupo->cd_grupo;
    }
    
    static function getIdEntidade()
    {
        return Zend_Auth::getInstance()->getIdentity()->id_entidade_atual;
    }

    static public function getPerfil()
    {
        return $role = 'usuario_' . self::getIdUsuario();
    }

    static public function getFoto()
    {
        return self::get('st_foto');
    }

    static public function getRole()
    {
        return 'usuario_' . self::getIdUsuario();
    }

    static public function getPerfis()
    {
        return Zend_Auth::getInstance()->getIdentity()->aPerfis;
    }

    static public function temPerfil($idPerfil)
    {
        return in_array($idPerfil, Zend_Auth::getInstance()->getIdentity()->aPerfis);
    }

    /**
     * Recuepra os recursos que o usuário tem acesso e que foram gravados na sessão
     * @return array
     */
    static public function pegarRecursos()
    {
        return Zend_Auth::getInstance()->getIdentity()->recursos;
    }

}
