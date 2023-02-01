<?php

// +----------------------------------------------------------------------
// | Simplestart CMS
// +----------------------------------------------------------------------
// | 版权所有: http://www.simplestart.cn copyright 2021
// +----------------------------------------------------------------------
// | 开源协议: https://www.apache.org/licenses/LICENSE-2.0.txt
// +----------------------------------------------------------------------
// | 仓库地址: https://github.com/simplestart-cn/start-cms
// +----------------------------------------------------------------------

namespace core\controller;

use PDO;
use start\Controller;
/**
 * 站点后台
 * Class Install
 * @package core\controller
 */
class Install extends Controller
{
	/**
	 * 安装引导
	 * @return [type] [description]
	 */
	public function index()
	{
		if (is_file(root_path() . '.env')) {
			if($this->request->isGet()){
				$this->redirect('/web/core/app');
			}else{
				$this->success('StartCMS安装成功！');
			}
		}
		if ($this->request->isPost()) {
			$input = $this->formValidate([
				'dbname.require' => '数据库名称不能为空',
				'dbuser.require' => '数据库用户不能为空',
				'dbpass.require' => '数据库密码不能为空',
				'dbhost.require' => '数据库地址不能为空',
				'dbport.default' => '3306',
				'dbcover.default' => false,
				'account.require' => '登录账户不能为空',
				'password.require' => '登录密码不能为空',
				'repassword.require' => '确认密码不能为空',
				'protocal.default'   => false,
			]);
			if (!$input['protocal']) {
				throw_error('请先阅读《StartCMS授权使用协议》');
			}
			if ($input['password'] !== $input['repassword']) {
				throw_error('登录密码与确认密码不相同');
			}
			
			$account = $input['account'];
			$password = password_hash($input['password'], PASSWORD_BCRYPT);
			
			try {
				$dbname = $input['dbname'];
				$dbuser = $input['dbuser'];
				$dbpass = $input['dbpass'];
				$dbhost = $input['dbhost'];
				$dbport = $input['dbport'];
				$dbcover = $input['dbcover'];
				$core = file_get_contents(root_path() . 'core' . DIRECTORY_SEPARATOR . 'installer' . DIRECTORY_SEPARATOR . 'core.sql');
				$data = file_get_contents(root_path() . 'core' . DIRECTORY_SEPARATOR . 'installer' . DIRECTORY_SEPARATOR . 'data.sql');

				$db = new PDO("mysql:host=$dbhost;port=$dbport", $dbuser, $dbpass);
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$dblist = $db->query('show databases')->fetchAll(PDO::FETCH_COLUMN);
				if (in_array($dbname, $dblist)) {
					$db->exec("use `$dbname`");
					if ($dbcover) {
						// 删除数据表并重新导入数据
						$sql = "SELECT concat('DROP TABLE IF EXISTS ', table_name, '') FROM information_schema.tables WHERE table_schema = '$dbname'";
						$tables = $db->query($sql)->fetchAll(PDO::FETCH_COLUMN);
						if (!empty($tables)) {
							$db->exec(implode(';', $tables));
						}
						$db->exec($core);
						$db->exec($data);
					}
				} else {
					// 创建数据表并导入数据
					$create = "CREATE DATABASE `$dbname` DEFAULT CHARSET utf8 COLLATE utf8_general_ci";
					$db->exec($create);
					$db->exec("use `$dbname`");
					$db->exec($core);
					$db->exec($data);
				}
				// 添加管理员
				$uuid = unique_id();
				$time = time();
				$user = $db->query("SELECT * FROM `core_user` where account = '$account';")->fetch();
				if ($user == false) {
					$db->exec("INSERT INTO `core_user` (`uuid`,`name`,`account`,`password`,`status`,`is_admin`,`is_super`,`login_ip`,`login_client`,`login_count`,`login_time`,`create_time`,`update_time`) VALUES ('$uuid', '系统管理员', '$account', '$password', 1, 1, 1,'', '' ,0 , 0, $time, $time)");
				} else {
					$db->exec("UPDATE `core_user` SET `password`= '$password', `status` = 1, `is_admin` = 1, `is_super` = 1 WHERE `id`=" . $user['id'] . ";");
				}

				// 添加环境配置
				$env = "APP_DEBUG = false\nAUTH_DEBUG = false\n\n[APP]\nDEFAULT_TIMEZONE = Asia/Shanghai\n[DATABASE]\nTYPE = mysql\nHOSTNAME = $dbhost\nDATABASE = $dbname\nUSERNAME = $dbuser\nPASSWORD = $dbpass\nHOSTPORT = $dbport\nCHARSET = utf8\nDEBUG = false";
				file_put_contents(root_path() . '.env', $env);
				$db = null;
				$this->success('安装成功！');
			} catch (\PDOException $e) {;
				throw_error($e->getMessage());
			}
		} else {
			$license = root_path() . 'LICENSE';
			$license = is_file($license) ? file_get_contents($license) : '';
			$license = nl2br($license);
			return $this->fetch('index', [
				'step'   => is_file(root_path() . '.env') ? 2 : 1,
				'domain'   => request()->domain() . request()->root(),
				'licenses' => $license,
			]);
		}
	}
	/**
	 * 数据库检查
	 * @return void
	 */
	public function checkDb()
	{
		$input = $this->formValidate([
			'dbname.require' => '数据库名称不能为空',
			'dbuser.require' => '数据库用户不能为空',
			'dbpass.require' => '数据库密码不能为空',
			'dbhost.require' => '数据库地址不能为空',
			'dbport.default' => '3306',
		]);
		$dbname = $input['dbname'];
		$dbuser = $input['dbuser'];
		$dbpass = $input['dbpass'];
		$dbhost = $input['dbhost'];
		$dbport = $input['dbport'];
		if (stripos($dbhost, ':') !== false) {
			$dbhost = explode(':', $dbhost);
			$dbport = array_pop($dbhost);
			$dbhost = array_pop($dbhost);
		}
		try {
			$db = new PDO("mysql:host=$dbhost;port=$dbport", $dbuser, $dbpass);
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$dblist = $db->query('show databases')->fetchAll(PDO::FETCH_COLUMN);
			if (in_array($dbname, $dblist)) {
				$this->success('数据库已存在，是否覆盖安装?', '', 2);
			} else {
				$this->success('数据库可用', '', 0);
			}
		} catch (\PDOException $e) {
			throw_error('数据库连接失败：' . $e->getMessage());
		}
	}
}
