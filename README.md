### 行政区划的概念

> 行政区域彼此之间有明确的地理分界线，并各自由自己的区政府、县政府、市政府直接管理，有自己的人大会。且有自己的上级和下级行政关系

[政区划代码对照表](http://www.mca.gov.cn/article/sj/xzqh/2018/)

一般也会存在一些经济区，如

合肥的高新区、新站区等这些区域不属于行政区

这些区域是地方政府为集中力量发展经济为目的而设置的特定区域，本质属于功能区，功能区不受行政区划限制，可以跨行政区

这些区域可以参考国家统计局：
[2017年统计用区划代码和城乡划分代码](http://www.stats.gov.cn/tjsj/tjbz/tjyqhdmhcxhfdm/2017/index.html)

[邮政编码对照表、城市电话区号对照表](http://www.ip138.com/post/)

### 数据

数据根据static/Region.php获取

```php
$item=[
    'code' => 110000,
    'parent_code' => 0,
    'name' => '北京',
    'postal_code' => 100000,
    'tel_code' => '010',
    'letter' => 'bei jing shi',
    'initial' => 'B',
    'type' => 1,// 1 是行政区域划分 0 不是
]

```

正式使用应该使用 static/RegionCode.php的数据，提取的是type为1的即规范的行政区域划分

### 注意

四个直辖市(北京、天津、上海、重庆)
在RegionCode.php中各自新增一个北京市、天津市、上海市、重庆市、充当省市区的市角色

