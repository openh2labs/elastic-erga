var path = 'build/js/components/my-component-3/build/';
var img = new Image();
img.src = [path,'cookie.jpeg'].join('');

document.querySelector('.div3').appendChild(img);
