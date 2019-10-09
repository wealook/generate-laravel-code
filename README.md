
zhuce          $app->register('Wealook\Generate\GenerateServiceProvider');

```
#生成model文件
 produce:model {name} {--table=}  {--connection=}  {{--force}}
```

* name 名称   Admin\\Config 带命名空间的 生成时model名与表名无关
* --table=表名        
* --connection  数据库连接名称,不传使用默认链接
* --force  强制覆盖已有文件
***
```
#生成model service repository controller route 文件
 produce:model_sr {name} {--table=}  {--connection=}  {--resource}  {--route=web}
```
* name 名称   Admin\\Config
* --table=表名        
* --connection  数据库连接名称
* --resource  resource风格路由
* --route  路由文件 默认web
* --view  视图文件目录(相对于resources/views) 分别生成 index.blade.php  create.blade.php  show.blade.php edit.blade.php


***

---
---