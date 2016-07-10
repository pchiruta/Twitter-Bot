# Twitter-Bot
This is the respository for twitter bot.

The welcome.php is the file which controls the flow.  This is the one to run to start the application.
This application runs through command line interface.

Features supported:
1. Given a user follow him.
   To do this invoke welcome.php this way: 
      php welcome.php uf <user_name>. 
  For eg "php welcome.php uf saibwaj".
2. Given a user reply-to his recent tweets.
   To do this invoke welcome.php this way:
    php welcome.php ur <user_name> <reply-to_response>
   For eg.  php welcome.php ur saibwaj "test response for saibwaj"
3. Given a user retweets his recent tweets.
   To do this invoke welcome.php this way:
    php welcome.php urt <user_name> 
   For eg.  php welcome.php urt saibwaj 
4. Follow 'n' no of users<users count can be made configurable> whose tweets matched the search query.
   To do this invoke welcome.php this way:
    php welcome.php sf <search_query> 
   For eg.  php welcome.php sf "testing"
5. Reply-to the tweets of recent 'n' users whose tweets matched the search query.
  To do this invoke welcome.php this way:
    php welcome.php sr <search_query> <sample_response> 
   For eg.  php welcome.php sr test "sample response for search term test"
6. Retweet the tweets matching the search query.
  To do this invoke welcome.php this way:
    php welcome.php srt <search_query>  
   For eg.  php welcome.php srt test 

TODO: Feeding twitter from RSS feeds

Note: This code uses Twitter OAuth class written by Abraham Williams. So its mandatory to install oauth related libs in php.
