<?php
namespace XT\Core\IDE\Config;
/***
 * Class ContactConfig
 * @property string descriptioncontact
 * @property string mailtransport
 * @property string recaptcha
 * @property string titlecontact
 * @property string titleformcontact
 * @property string emailcontact
 */
class ContactConfig
{
    //name
    //hint
    //description
    //value
    //type
    public static $data = [
        ['descriptioncontact','Description','Description in meta tag','Contact me!','string','contactsite'],
        ['mailtransport','Mail Transport','Mail Transport:sendmail or smtp or file','sendmail','string','contactsite'],
        ['titlecontact','Title','Title in page contact','Contact US','string','contactsite'],
        ['titleformcontact','Form Title','Title form contact','Fill information and click send','string','contactsite'],
        ['recaptcha','Recaptcha','Use Recaptcha',false,'boolean','contactsite'],
        ['emailcontact','Email','email address receive','administrator@yourwebsite.com','string','contactsite'],

       // ['foldermail','Foder lưu trữ email','Foder lưu trữ email - cho trường hợp chọn file','data\\mail','string','contactsite'],

    ];
}