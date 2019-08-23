import pymysql, time, random, json

class DbClass:
    
    dsn = None
    prepare_parm_list = []
    whereParse = ""
    fieldParse = "*"
    limitParse = ""
    orderParse = ""
    tableName = ""
    sql = ""
    __private_attr = "bilibili"

    def __init__(self, config = []):
        # 初始化操作
        host = config['host']
        user = config['user']
        passwd = config['passwd']
        database = config['database']
        charset = config['charset']
        option = config['option']

        self.dsn = pymysql.connect(host=host, user=user, password=passwd, database=database, charset=charset, cursorclass=option)

    def table(self, table=""):
        # 数据表名称
        self.tableName = table
        return self

    def where(self, where=[]):
        # 条件查询
        for key, val in where.items():
            if isinstance(val, list):
                if val[0] == "between":
                    self.whereParse += key + " " + val[0] + " %s and %s and "
                    self.prepare_parm_list.append(str(val[1][0]))
                    self.prepare_parm_list.append(str(val[1][1]))
                    continue
                elif val[0] in [">", "<", ">=", "<=", "<>"]:
                    self.prepare_parm_list.append(str(val[1]))
                    self.whereParse += key + " " + val[0] + " %s and "
                    continue
                elif val[0] == "in":
                    id_list = val[1]
                    prepare_count = "%s," * (id_list.count(",") + 1)
                    prepare_count = prepare_count.rstrip(",")
                    self.prepare_parm_list.extend(id_list.split(","))
                    self.whereParse += key + " " + val[0] + "(" + prepare_count + ") and "
                    continue
            self.whereParse += key + "=%s and "
            self.prepare_parm_list.append(str(val))

        # self.whereParse = self.whereParse.rstrip()
        # self.whereParse[4:len(self.whereParse)]
        self.whereParse = self.whereParse.rstrip(" and ")
        return self

    def field(self, field):
        if isinstance(field, str):
            self.fieldParse = field

        if isinstance(field, dict):
            # 以逗号拆分
            pass
        return self

    def limit(self, limit = 16):
        # 设置行数
        self.limitParse = "limit " + str(limit)
        return self
    
    def order(self, order = ""):
        # 设置排序
         self.orderParse = "order by " + order
         return self

    def find(self):
        # 查找单条数据
        self.sql = "select " + self.fieldParse + " from " + self.tableName + " where " + self.whereParse + " " + self.orderParse + " " + self.limitParse
        cursor = self.dsn.cursor()
        cursor.execute(self.sql)
        result = cursor.fetchone()
        return result
    
    def select(self, limit = False):
        if limit != False & isinstance(limit, int):
            self.limitParse = "limit " + str(limit)
        
        # 查找多条数据
        self.sql = "select " + self.fieldParse + " from " + self.tableName + " where " + self.whereParse + " " + self.orderParse + " " + self.limitParse

        cursor = self.dsn.cursor()
        cursor.execute(self.sql, self.prepare_parm_list)
        result = cursor.fetchall()
        cursor.close()
        return result

    def insert(self, data):
        if isinstance(data, dict) == False:
            # isinstance(data, list) == False | 
            raise '错误的数据!'
        # 编译sql语句
        prepare = ""
        parm = ""
        data_value = []
        for key, val in data.items():
            prepare += key + ","
            parm += "%s,"
            data_value.append(val)
        prepare = prepare.strip(",")
        parm = parm.strip(",")
        # 准备执行sql
        self.sql = "insert " + self.tableName + "(" + prepare + ") values (" + parm + ")"
        cursor = self.dsn.cursor()
        try:
            # 写入数据
            bool_back = cursor.execute(self.sql, data_value)
            if bool_back == 0:
                return "新增失败!"
            self.dsn.commit()

        except Exception as error:
            self.dsn.rollback()
            raise error

        return cursor.lastrowid

    def insertMany(self, data):
        if isinstance(data, dict) == False:
            raise '错误的数据!'
        # 编译sql语句
        prepare = ""
        parm = ""
        data_one = data[0]
        data_value = []
        for val in data_one:
            prepare += val + ","
            parm += "%s,"
        prepare = prepare.strip(",")
        parm = parm.strip(",")
        
        # 编译数据
        for val_d in data.values():
            data_value.append(list(val_d.values()))

        self.sql = "insert " + self.tableName + "(" + prepare + ") values (" + parm + ")"
        cursor = self.dsn.cursor()
        try:
            # 写入数据
            bool_back = cursor.executemany(self.sql, data_value)
            if bool_back == 0:
                return "批量新增失败!"
            # 提交事务
            self.dsn.commit()

        except Exception as error:
            # 失败后回滚数据
            self.dsn.rollback()
            raise error

        return bool_back

    def __privateFunc(self):
        return "你这是在为难我胖虎!" + self.__private_attr

    def __del__(self):
        self.dsn.close()
        print("---------------------is over---------------------")


