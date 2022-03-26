const { mix } = require("laravel-mix");
require("laravel-mix-merge-manifest");

if (mix.inProduction()) {
    var publicPath = 'publishable/assets';
} else {
    var publicPath = "../../../public/vendor/webkul/mobikul/assets";
}

mix.setPublicPath(publicPath).mergeManifest();
mix.disableNotifications();

mix.js(
    [
        __dirname + "/src/Resources/assets/js/app.js"
    ],
     "js/mobikul.js"
)
    .copyDirectory(__dirname + "/src/Resources/assets/images", publicPath + "/images")
    .sass(__dirname + "/src/Resources/assets/sass/admin.scss", "css/mobikul.css")
    .options({
        processCssUrls: false
    });

if (mix.inProduction()) {
    mix.version();
}
