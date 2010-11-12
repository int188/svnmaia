关于自定义插件：
你可以自定义插件集成到本系统中，只需要按照如下格式添加到文件 ./addon.ini(请自己创建) 中：

[说明文字]
url='相对或绝对url链接'
target=_blank或''

如果target=_blank，则插件将在新窗口打开，如果没有定义，则在本窗口打开。
例子：

[创建库工具]
url='./createDB.php'
[备份库工具]
url='./backup.php'
target=_blank