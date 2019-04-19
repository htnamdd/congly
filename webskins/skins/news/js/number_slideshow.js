(function(a){
    a.fn.number_slideshow=function(p){
        var p=p||{};

        var n=p&&p.slideshow_autoplay?p.slideshow_autoplay:"disable";
        var o=p&&p.slideshow_time_interval?p.slideshow_time_interval:"10000";
        var q=p&&p.slideshow_window_background_color?p.slideshow_window_background_color:"white";
        var r=p&&p.slideshow_window_padding?p.slideshow_window_padding:"0";
        var s=p&&p.slideshow_window_width?p.slideshow_window_width:"220";
        var t=p&&p.slideshow_window_height?p.slideshow_window_height:"130";
        var u=p&&p.slideshow_border_size?p.slideshow_border_size:"1";
        var v=p&&p.slideshow_border_color?p.slideshow_border_color:"black";
        var w=p&&p.slideshow_show_button?p.slideshow_show_button:"enable";
        var x=p&&p.slideshow_button_text_color?p.slideshow_button_text_color:"#17507B";
        var z=p&&p.slideshow_button_background_color?p.slideshow_button_background_color:"white";
        var A=p&&p.slideshow_button_current_background_color?p.slideshow_button_current_background_color:"#F5F8F3";
        var B=p&&p.slideshow_button_border_color?p.slideshow_button_border_color:"#c3ccd4";
        var C=p&&p.slideshow_button_border_size?p.slideshow_button_border_size:"1";

        r += "px";
        s += "px";
        t += "px";
        u += "px";
        C += "px";
        var D;
        var E=0;
        var F=a(this);
        var G=F.find("ul:first").children("li").length;
        if(F.find("ul").length==0||F.find("li").length==0){
            a.append("Require content");
            return null
        }
        F.show();
        F.find("ul:first").children("li").children("a").children("img").css("width",s).css("height",t);
        s_s_ul(F.find("ul:first"),r,s,t,u,v,q,z,x);
        //s_s_n(F.find(".number_slideshow_nav"),x,w,z,A,B,C);//INITIALISATION DES PAYS : DESACTIVEE
        F.find("ul:first").children("li").hide();
        play();
        F.find("ul:first").hover(function(){
            clearTimeout(D);
        },function(){
            D=setTimeout(play,o);
        });
        F.find(".number_slideshow_nav").children("li").hover(function(){
            stop($(this))
        });
        function play(){
            if(n=="enable"){
                clearTimeout(D);
                F.find("ul:first").children("li").fadeOut();
                F.find("ul:first").children("li").eq(E).fadeIn();
                F.find(".number_slideshow_nav").children("li").css("background-color",z);
                F.find(".number_slideshow_nav").children("li").eq(E).css("background-color",A);
                E++;
                if(E>=G){
                    E=0
                }
                D=setTimeout(play,o)
            }else{
                F.find("ul:first").children("li").eq(E).fadeIn();
            }
        }
        function stop(a){
            clearTimeout(D);
            var b=a.parent().children().index(a);
            if(b != (E-1)){
            E=b+1;
            if(E>=G){
                E=0
            }
            F.find("ul:first").children("li").hide();
            F.find("ul:first").children("li").eq(b).show();
            F.find(".number_slideshow_nav").children("li").css("background-color",z).css("border-left","1px solid white").css("border-top","1px solid white").css("border-right","1px solid white").css("border-bottom","1px solid #c3ccd4");
            F.find(".number_slideshow_nav").children("li").eq(b).css("background-color",A).css("border-left","1px solid #c3ccd4").css("border-top","1px solid #c3ccd4").css("border-right","1px solid #c3ccd4").css("border-bottom","1px solid #f5f8f2");//ajout border sur survol
            }
        }
        function s_s_ul(a,b,c,d,e,f,g,h,i){
            a.children("li").css("width",c).css("height",d);
            //a.children("li").each(function(i){
            //     a.children("li").eq(i).append("<p>"+a.children("li").eq(i).children("a").children("img").attr("alt")+"</p>");
            //});
            //a.children("li").children("p").css("background-color",h).css("color",i).css('width',parseInt(c)-10);
            //a.children("li").children("p").css('width',parseInt(c)-10);
            b=parseInt(b);
            c=parseInt(c);
            d=parseInt(d);
            e=parseInt(e);
            var h=c+e*2+b*2;
            var i=d+e*2+b*2;
            F.css("width",h);
            F.css("height",i);
            var j=d+"px";
            var k=c+"px";
            var l="border: "+f+" solid "+" "+e+"px;height:"+j+"; width:"+k+"; padding:"+b+"px; background-color:"+g;//Background photo
            a.attr("style",l)
        }
        function s_s_n(b,c,d,e,f,g,h){
            h=parseInt(h);
            var j=b.children("li");
            var a=j.children("a");
            a.css("color",c);
            var k="border: "+g+" solid "+" "+h+"px; background-color:"+e+";";
            var k="border: "+g+" solid "+" "+h+"px; background-color:red;";
            j.attr("style",k);
            if(d!="enable"){
                b.hide()
            }
        }
    }
})(jQuery);