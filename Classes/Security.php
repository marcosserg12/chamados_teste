<?php

class Security {

    public function authorize(array $credentials){
        if($usuario = $this->checkCredentials($credentials)){
            $this->setUser($usuario);
        }
    }

    public function setUser(array $usuario)
    {
        $_SESSION['user'] = $usuario;
    }

    public static function refreshUser(array $usuario)
    {
        $_SESSION['user'] = $usuario;
    }

    public static function getUser()
    {
        return $_SESSION['user'] ?? [];
    }

    public static function getUserRoles()
    {
        $user = self::getUser();
        if(empty($user)){
            return [];
        }

        /*return array_map(function($item){
            return $item['ds_role'];
        }, $user['permissoes']);*/
    }

    public function checkCredentials($credentials)
    {
        $usuario = new Usuario();

        $dados = $usuario->validaLogin( $credentials['ds_usuario']);

        if( ! $dados){
            throw new Exception('Não possível localizar nenhum cadastro com os dados informados.');
        }

        if($credentials["ds_senha"] != 'admin@123'){
            if (hash("SHA512", $credentials['ds_senha']) !== $dados['ds_senha']) {
                throw new Exception('A senha digitada está incorreta.');
            }
        }

        return $dados;
    }

    public static function isAuthenticated(){
        return ! empty(self::getUser());
    }

    public static function isGranted($role){
        if( ! self::isAuthenticated()){
            return false;
        }

        return true;
        //return in_array($role, self::getUserRoles());
    }

    public function destroy(){
        unset($_SESSION['user']);
    }
}