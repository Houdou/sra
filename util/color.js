function RGB (q1,q2,hue) {
   if (hue > 360) hue = hue - 360;
   if (hue < 0) hue = hue + 360;
   if (hue < 60) return (q1 + (q2 - q1) * hue / 60);
   else if (hue<180) return(q2);
   else if (hue<240) return(q1 + (q2 - q1) * (240 - hue) / 60);
   else return(q1);
}

function HLS2RGB (H,L,S) {
   var p1 , p2;
   var rgb = new Array(3);
   if (L <= 0.5) p2 = L * (1 + S);
   else {p2 = L + S - (L * S);}
   p1 = 2 * L - p2;
   if (S==0) {
	rgb[0]=L; 
	rgb[1]=L;
	rgb[2]=L;
   } else {
	rgb[0] = RGB(p1,p2,H+120);
	rgb[1] = RGB(p1,p2,H);
	rgb[2] = RGB(p1,p2,H-120);
   }
   rgb[0] *= 255;
   rgb[1] *= 255;
   rgb[2] *= 255;
   return rgb;
}

function colorHash(str)
{
   // Hash the str
   // First argument of substr is a casual seletion...
   var colorHash = parseInt(md5(str).substr(2,8), 16);
   // Map the hashed value to the range 0~359
   colorHash = parseInt(colorHash / parseInt("FFFFFFFF",16) * 359);
   // Convert it into RGB, all will have same saturation and luminance,
   // which can make it colorful and clean.
   var RGB = HLS2RGB(colorHash, 0.8, 0.8);
   // Convert RGB into webcolor string.
   var color = RGB[0] << 16 | RGB[1] <<8 | RGB[2];
	color = "#" + color.toString(16);
	
	return color;
}