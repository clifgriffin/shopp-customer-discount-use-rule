# Shopp Customer Discount Use Rule

This is a WordPress plugin with no options.  It adds a new discount rule type labeled 'Customer discount use count' which allows you to limit the number of times a discount can be used by a single customer. 

## What do I mean by customer? ##

For a bunch of reasons I won't explain here, it uses the email address at time of order (or at the time the discount is added if available) and looks up any previous orders using that email address and discount ID (yes, ID, not discount code). 

So, someone can skirt it if they know to try a different email.  But the error message is generic enough they probably won't think of that. 

## Other Gotchas ##
 
A patch to Shopp 1.3.10 was required to make this plugin possible. The good news, it's super easy to apply to your Shopp 1.3.10 <= install. Just look at this commit: 
https://github.com/ingenesis/shopp/commit/4468627b4be43c5b4191341b0969c64d58fc4b68

The gooder news? This patch has been applied to core Shopp so it will be included in all future releases. #winning
