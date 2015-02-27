$(document).ready(function(){
    function meteor(cxt){
        this.content = document.getElementById(cxt);
        if(!this.content){
            alert("你没有设ｃａｎｖａｓ对像");
        }
        var cxt = this.content.getContext('2d')
        this.x = -1;
        this.y = -1;
        this.width = 182;
        this.height = 102;
        this.speed = 0.05;
        this.alpha = 1; //透明度
        this.angle = 1; //以上参数分别表示了流星的坐标、速度和长度
        this.rotate = 20;//旋转　
        this.scale = 0;//扩大倍数

        /**************获取随机颜色函数*****************/
        this.getRandomImage = function () //
        {
            var pic = [
                {src:"cloud1.png",w:182,h:102},
                {src:"cloud2.png",w:158,h:95},
                {src:"cloud3.png",w:114,h:67},
                {src:"cloud4.png",w:133,h:79},
                {src:"cloud5.png",w:98,h:55}
            ];
            var dom = pic[Math.floor(Math.random()*5)];
            this.width = dom.w;
            this.height = dom.h;
            this.src = dom.src;
        }

        /***************重新计算流星坐标的函数******************/
        this.countPos = function ()//
        {
            this.x = this.x - this.speed * Math.cos(this.angle * 3.14 / 180);
            this.y = this.y + this.speed * Math.sin(this.angle * 3.14 / 180);
        }
        /*****************获取随机坐标的函数*****************/
        this.getPos = function () //
        {
            /*
            * document.write(Math.random()*(n-m)+m);
             返回指定范围的随机数(m-n之间)的公式
             */
            var dom =this.getPosition();
            this.x = dom.x;
            this.y = dom.y;
          //  this.angle = 90; //假设流星倾斜角30
        }

        /****绘制单个流星***************************/
        this.drawSingleMeteor = function () //绘制一个流星的函数
        {
            cxt.save();
          //  cxt.globalAlpha =Math.random();
            var img = new Image();
            img.src="./star/"+this.src;
            cxt.drawImage(img,this.x,this.y,this.width,this.height);
            cxt.restore();

        }
        /****************初始化函数********************/
        this.init = function () //初始化
        {
            this.getPos();
            this.alpha = 1;
            this.getRandomImage();
        }
    }
    meteor.prototype = {
        dataRes:(function(){
            var maxX = 182;
            var maxY = 102;
            var x = document.getElementById("canvas1").clientWidth;
            var y = document.getElementById("canvas1").clientHeight;
            var xarr = Math.floor(x/maxX),yarr=Math.floor(y/maxY);
            var ret =[];
            for(var i= 0,len= xarr;i<len;i++){
                ret[i] = [];
                for(var j= 0,len1 = yarr;j<len1;j++){
                    ret[i][j] = 0;
                }
            }
            return ret;
        })(),
        getPosition:function(){
            var x1 = Math.floor(Math.random()*this.dataRes.length);
            var y1 = Math.floor(Math.random()*this.dataRes[this.dataRes.length-1].length);
            while(this.dataRes[x1][y1]!==0){
                x1 = Math.floor(Math.random()*this.dataRes.length);
                y1 = Math.floor(Math.random()*this.dataRes[this.dataRes.length-1].length);
            }
            this.dataRes[x1][y1] =1;
            return {
                x:x1*182,
                y:y1*102
            };
        }
    }
    var Meteors = [];
    var cxt=document.getElementById("canvas1").getContext('2d');
    //cxt.globalAlpha=0.8;
    var MeteorCount = 5;
    for (var i = 0; i < MeteorCount; i++) //;
    {
        Meteors[i] = new meteor("canvas1");
        Meteors[i].init();//初始化
        Meteors[i].drawSingleMeteor();
    }
    function playMeteors() //流星
    {
        for (var i = 0; i < MeteorCount; i++) //循环处理
        {
            var w=Meteors[i].speed*Math.cos(Meteors[i].angle*3.14/180);
            var h=Meteors[i].speed*Math.sin(Meteors[i].angle*3.14/180);
            cxt.clearRect(Meteors[i].x-3,Meteors[i].y-3,Meteors[i].width+10,Meteors[i].height+5);
            Meteors[i].drawSingleMeteor();
            Meteors[i].countPos();
            if (Meteors[i].y>320) //到达下线
            {
                Meteors[i] = new meteor("canvas1");
                Meteors[i].init();
            }
        }
    }
   setInterval(playMeteors,30);
});


