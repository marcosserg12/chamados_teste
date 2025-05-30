<?php

	class Conecta {

		static private $db;


		private static function criarConexao(){
			try
			{
				$config = require __DIR__ . '/../config.php';

				self::$db = new PDO(
					sprintf('mysql:host=%s;dbname=%s;port=%s;charset=utf8', $config['database']['host'], $config['database']['dbname'], $config['database']['port']),
					$config['database']['user'],
					$config['database']['password']
				);

				self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				self::$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

				return self::$db;
			}
			catch ( PDOException $e )
			{
				die( 'Erro ao conectar com o Banco: ' . $e->getMessage());
			}
		}

		static public function getConexao()
		{
			/*if(self::$db instanceof PDO)
			{
				return self::$db;
			}*/

			return self::criarConexao();
		}
	}


