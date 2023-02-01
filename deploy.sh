# 云效自动化部署脚本
# 删除所有空行
sed -i "/^[[:space:]]*$/d" ./.env
# 行尾添加字符
sed -i 's/$/&;/g' ./.env
# 关闭调试模式
sed -i "s/^APP_DEBUG\s*=.*;/APP_DEBUG = false;/g" ./.env
sed -i "s/^AUTH_DEBUG\s*=.*;/AUTH_DEBUG = false;/g" ./.env
# 配置数据库连接
sed -i "s/^HOSTNAME\s*=.*;/HOSTNAME = ${dbhost};/g" ./.env
sed -i "s/^DATABASE\s*=.*;/DATABASE = ${dbname};/g" ./.env
sed -i "s/^USERNAME\s*=.*;/USERNAME = ${dbuser};/g" ./.env
sed -i "s/^PASSWORD\s*=.*;/PASSWORD = ${dbpass};/g" ./.env
sed -i "s/^HOSTPORT\s*=.*;/HOSTPORT = ${dbport};/g" ./.env
# 删除每行最后一个字符
sed -i "s/.$//g" ./.env
# 删除所有空行
sed -i "/^[[:space:]]*$/d" ./.env