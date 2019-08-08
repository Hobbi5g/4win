#!c:/perl/bin/perl -w
#!/usr/bin/perl
#!c:/perl/bin/perl -w

#!/usr/local/bin/perl
#use strict;
use CGI::Carp qw(fatalsToBrowser);

require 'cgi-bin2/database.pl';

require 'cgi-bin2/var.pl';
require 'cgi-bin2/common.pl';
require 'cgi-bin2/form.pl';


my $font = 'abcdefghijklmnopqrstuvwxyz0123456789';

#bool TMainMenu::CheckOwner()
#{
#    int sum = 0;
#    char str[4] = {0};
#    DWORD n;

#    for(int c=0; c < name_str.length(); ++c)
#        sum += name_str[c];

#    char ch0 = font[1*sum%36];
#    char ch1 = font[2*sum%36];
#    char ch2 = font[3*sum%36];

#}

#http://www.reflectorstudio.com/__wlkg__.cgi?name=reggie
#http://www.games4win.com/__wlkg__.cgi?name=reggie

my $ch0 = '';
my $ch1 = '';     
my $ch2 = ''; 


sub CheckOwner($)
{
    my $c;
    my $sum=0;

    for($c=0; $c<length($_[0]); ++$c)
   {
        $sum += ord(substr($_[0],$c, 1));
    }

    $ch0 = substr($font,1 * $sum % 36, 1);
    $ch1 = substr($font,2 * $sum % 36, 1);
    $ch2 = substr($font,3 * $sum % 36, 1);
    
}


#my $userName = $ARGV[0];
my $userName = param("name");
$userName = lc($userName);

my $regCode = '';    
$contenttype = "Content-Type: text/plain\n\n";

# Code, function calls, etc, to handle
# registration/license generation

BaseConnect();
BaseSelect("SELECT id, prekey FROM rs_static_keys WHERE active=1 ORDER BY id ASC LIMIT 0, 1");
my @res=BaseFetch();
BaseFinish();

BaseDo("UPDATE rs_static_keys SET active=0 WHERE id=$res[0] ");
BaseDisconnect();

$regCode=$res[1];
print $contenttype;
#print "$userName \n";
CheckOwner($userName);
$regCode="$regCode$ch0$ch1$ch2";

print "$regCode\n";

