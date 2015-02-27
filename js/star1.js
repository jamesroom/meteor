$(document).ready(function(){
    function meteor(){
        this.x = -1;
        this.y = -1;
        this.length = -1;
        this.width = -1;
        this.speed = -1;
        this.alpha = 1; //透明度
       // this.angle = -1; //以上参数分别表示了流星的坐标、速度和长度


        /**************获取随机颜色函数*****************/
        this.getRandomImage = function () //
        {
           var pic = [
                {src:"1.png",w:80,h:68},
                {src:"2.png",w:110,h:89},
                {src:"3.png",w:75,h:65},
                {src:"4.png",w:147,h:123}
            ];
            var dom = pic[Math.floor(Math.random()*4)];
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
            this.x = Math.ceil(Math.random() * document.documentElement.clientWidth);
            this.y = Math.ceil(Math.random() * 200);
            this.angle = 30; //假设流星倾斜角30
            this.speed = 5; //假设流星的速度
        }

        /****绘制单个流星***************************/
        this.drawSingleMeteor = function () //绘制一个流星的函数
        {
            cxt.save();
            var img = new Image();
            img.src="./star/"+this.src;
            cxt.drawImage(img,this.x,this.y,this.width,this.height);
           cxt.restore();
           /* cxt.save();
            cxt.beginPath();
            cxt.lineWidth = this.width;
            cxt.globalAlpha = this.alpha; //设置透明度
            var line = cxt.createLinearGradient(this.x, this.y, this.x + this.length * Math.cos(this.angle * 3.14 / 180), this.y - this.length * Math.sin(this.angle * 3.14 / 180)); //创建烟花的横向渐变颜色
            line.addColorStop(0, "white");
            line.addColorStop(0.1, this.color1);
            line.addColorStop(0.6, this.color2);
            cxt.strokeStyle = line;
            cxt.moveTo(this.x, this.y);
            cxt.lineTo(this.x + this.length * Math.cos(this.angle * 3.14 / 180), this.y - this.length * Math.sin(this.angle * 3.14 / 180));
            cxt.closePath();
            cxt.stroke();
            cxt.restore();*/
        }
        /****************初始化函数********************/
        this.init = function () //初始化
        {
            this.getPos();
            this.alpha = 1;
            this.getRandomImage();
        }
    }

    var Meteors = [];
    var cxt=document.getElementById("canvas").getContext('2d');
    //cxt.draw
    $("#canvas3").attr("width",document.documentElement.clientWidth);
    var MeteorCount = 10;
    for (var i = 0; i < MeteorCount; i++) //;
    {
        Meteors[i] = new meteor(cxt);
        Meteors[i].init();//初始化
        Meteors[i].drawSingleMeteor();
    }

    function playMeteors() //流星
    {
        for (var i = 0; i < MeteorCount; i++) //循环处理
        {
            var w=Meteors[i].speed*Math.cos(Meteors[i].angle*3.14/180);
            var h=Meteors[i].speed*Math.sin(Meteors[i].angle*3.14/180);
            cxt.clearRect(Meteors[i].x,Meteors[i].y,Meteors[i].width,Meteors[i].height);
            Meteors[i].drawSingleMeteor();
            Meteors[i].countPos();
            Meteors[i].alpha -= 0.002;
            if (Meteors[i].y>320||Meteors[i].alpha<=0.01) //到达下线
            {
               // cxt.clearRect(Meteors[i].x - 1, Meteors[i].y - Meteors[i].length * Math.sin(Meteors[i].angle * 3.14 / 180), Meteors[i].length * Math.cos(Meteors[i].angle * 3.14 / 180)+2, Meteors[i].length * Math.sin(Meteors[i].angle * 3.14 / 180)+2);
                Meteors[i] = new meteor(cxt);
                Meteors[i].init();
            }
        }
    }
    $(window).resize(function(){
        $("#canvas3").attr("width",document.documentElement.clientWidth);
    });
   setInterval(playMeteors,30);
});


