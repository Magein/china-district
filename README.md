### 名称更新

2022-03-25名称由region更新为china-district

使用district的原因：

1. 对接腾讯、高德、百度等api的时候，行政区域都是用district命名的
2. 在查询了一些资料后，使用district更贴切

[region|district|zone|area区别](./description.md)

### 行政区划

> 行政区域彼此之间有明确的地理分界线，并各自由自己的区政府、县政府、市政府直接管理，有自己的人大会。且有自己的上级和下级行政关系

[政区划代码对照表](http://www.mca.gov.cn/article/sj/xzqh/2018/)

### 城市的经济区、开发区

在除国家划分的行政区划外，一般城市也会存在一些经济区，如"合肥的高新区"、"新站区"等这些本地人知道的区域

他们不属于行政区，这些区域是地方政府为集中力量发展经济为目的而设置的特定区域，本质属于功能区，功能区不受行政区划限制，可以跨行政区

参考国家统计局：
[2017年统计用区划代码和城乡划分代码](http://www.stats.gov.cn/tjsj/tjbz/tjyqhdmhcxhfdm/2017/index.html)

[邮政编码对照表、城市电话区号对照表](http://www.ip138.com/post/)

### 数据

数据根据static/District.php获取

```php
[
    // id，行政区划代码
    'id' => 110000,
    // 父集id
    'parent_id' => 0,
    // 名词
    'name' => '北京',
    // 邮编
    'postal' => 100000,
    // 区号
    'tel' => '010',
    // 拼音
    'letter' => 'bei jing shi',
    // 首字母
    'initial' => 'B',
    // 1 是行政区域划分 0 不是
    'type' => 1,
]

```

正式使用应该使用 static/DistrictCode.php的数据，提取的是type为1的，即标准的行政区域划分

### 注意

四个直辖市(北京、天津、上海、重庆)
在DistrictCode.php中各自新增一个北京市、天津市、上海市、重庆市、充当省市区的市角色以便于三级联动

