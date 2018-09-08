<?php
namespace XT\Core\IDE\Config;
/***
 * Class CommonConfig
 * @property string titlesite
 * @property string description
 * @property string keywords
 * @property string googleanalytic
 * @property string rootpath
 * @property string pathtemplatedefault
 */
class CommonConfig
{

    //name
    //hint
    //description
    //value
    //type
    public static $data = [
        [
            'titlesite',
            'Title Site',
            'This data use in meta tag for home page, common page ...',
            'Website base on XTLAB',
            'string'
        ],

        [
            'description',
            'Description',
            'Description text use for meta in header',
            'XTLAB is website building with Zend Framework (PHP 7.0)',
            'string'],
        [
            'keywords', 'Keywords','List keywords use in meta keywork','','string'
        ],

        [
            'googleanalytic',
            'Google Analytic',
            'CODE Google Analytic',
            '',
            'string'
        ],




//        ['directorpluginviewr','Thư mục Plugin Viewer','Thư mục Plugin Viewer','/../../../../Pluginview/src/Pluginview/','string'],
//        ['fileextupload','File EXT','Các file được phép upload:gif,jpg,jpeg,png','gif,jpg,jpeg,png','string'],
//        ['loadingimg','Ảnh Loading','Hình ảnh hiện thị loading','http://www.bepxinh.vn/public/loading.gif','string'],
//        ['trademark','Dòng Trademark','Dòng Trademark: Logo và Thương hiệu - BEPXINH.VN đã được bảo hộ','Logo và Thương hiệu - BEPXINH.VN đã được bảo hộ','string'],
//        ['showfirstfooter','Hiện thị First Footer','Soạn thảo tại Footer:firstfooter.phtml',1,'boolean'],
//        ['cateservice','Category Service','Category tích hợp với Service',87,'integer'],
//        ['navafter','Nav After','Cột menu trước hay sau?',false,'boolean'],
//        ['templateservice','Template Service','HTML Template for Service','services/main-services/index.phtml','string'],
//        ['columnright','Cột Phải','Cột đứng ở phải hay trái',false,'boolean'],
//        ['notdirectorynumber','Not User DiNum','Không sử dụng số cho Thư mục',false,'boolean'],
//        ['nogooglefont','No Google Font','Không load google font',false,'boolean'],
//        ['isnguoinoitieng','WebNNT','Web Nguoinoitieng',false,'boolean'],
//        ['showsocialmedia','Hiện thị đăng nhập Social','Hiện thị đăng nhập Social',false,'boolean'],
//        ['allowregister','Cho phép đăng ký','Cho phép đăng ký thành viên mới',false,'boolean'],
//        ['allowregisteronlyfromsocial','Cho phép đăng ký chỉ từ Social','Chỉ đăng ký từ Fb, Gg, Twitter',false,'boolean'],
//        ['tagcategory','Hiện thị Tag Category','Hiện thị Tag Category thay cho Category Thường',false,'boolean'],
//        ['allownewtag','Cho phép tạo tag mới từ CSM Edit','True thì có thể thêm tag mới, nếu không chỉ sử dụng tag đã có, hoặc hệ thống không sử dụng bảng tag',false,'boolean'],


    ];
}