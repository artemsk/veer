x Change output style in database queues to '$this->info'
x Move error handlers to service provider
x Move booting from globals.php to service provider
x Move 'helpers.php' from Laravel 5
x Slightly rewrite base controller method 'construct'
x Slightly rewrite base controller method 'destruct'
x Take shopping cart items quantity from session instance
x Receive session messages with events.php
x Output session messages with basic template
x Move templates files to new folders
x Get '$title' and site variables for pages; google analytics
-------------------------------------------------- 0.1.7-alpha
x Update page, product, category, search, user views
x Product price format: currency converter, sales, views templates etc.
x Product price format: consider user discounts / user roles
x Product: create basket link
x Add to basket using ajax requests
x Updating basket items quantity
x Enhance adding to basket visually 
x Add new field to userlists?
x Adding products with attributes to shopping cart
x Callme component
--------------------------------------------------- 0.1.8-alpha
x One way to speed up price formatting: store role_id & auth:id in session
x Redesign footer @html
x (web template) Redesign message center message style @html
x Redesign callme component @html
x Prepare all controllers with basic actions
x Secret codes (passwords) for getting orders data (unreg users etc.)
x Disqus comments
--------------------------------------------------- 0.1.9-alpha
x Administration: sites & attributes templates
x Administration: all categories templates
x Administration: tags
x Administration: images
x Administration: downloads templates
x Administration: pages, products, settings & filters templates
x Administration: page & product template
x Installation Artisan Command (without configuration)
x Administration: Add/update sites
x Administration: Add/update/delete configuration
x Administration: show products, pages, images for category
x Administration: Add/update/delete components
x Administration: connect existing images to other elements
x Administration: secrets, jobs
x Administration: get trashed elements
x Administration: working with categories (sub/parent/current)
x Administration: create jobs (as components!)
---------------------------------------------------- 0.3.0-alpha
x Administration: sort categories with drag & drop (manual_sort, ajax)***END OF FILE***
Administration: ! check before delete (popup)
Administration: category page: add/remove images, products, pages
Administration: search
Administration: links to products & pages from elements
Administration: add, update & delete attributes
Administration: separate page for single attribute (update, add, delete)
Administration: cross links to communications, comments
Administration: show comms for category
Administration: when making download links expiration date or times should be set
Administration: clear cache, show migrations, show password reminders
Administration: test showing parent & subproducts
Administration: deprecate groups for subproducts?
Administration: markdown syntax for text editor
Administration: deprecate in_last & in_news fields for pages
Administration: real ajax on configuration pages
Administration: clear trash
Administration: restore() functions for categories/images/pages/products

(web template) product page
(web template) page
(web template) login/secretcode page
Sort - default
Loading more products on request with ajax [pagination]
Filter - choices + db; default
Filter - action
Search - defaults
Convert old products table to new
Simple website example 1
Simple website for bolshaya.net
Initial admin touch
(C) Sort products: fields: dat, price, star, ordered, viewed, status, type, category
(C) Sort products: ajax
-----------------------------------------------------------------------------
Component: chosen attribute
Component: list of popular tags
Component: list of attributes
Component: list of categories
Component: popular products
Component: banners
Component: basket ?
Component: comments form ***
Component: comments output
Component: compare
Component: google news
Component: url parsing ? (yogaclassic->flickr)
Component: chosen pages
Component product: same attributes, categories, tags, chosen attributes
Component: sapelinks
Plugin: images extender thumbnails? slides full screen etx. fancybox
Plugins: shipping - types
Plugins: payments
(+) Actions: basket
(+) Actions: compare
(+) Actions: contact/communications
(+) Actions: login
(+) Actions: logout
(+) Actions: register
(+) Actions: remember
Url: order bills
Basket: Ajax -> redraw
Plugins: numbers in russian
x Actions: secret codes
--------------------------------------
When creating product page get all attributes and detect which of them change price
Gathering categories, attributes, tags, popular products by views, orders - check
Gathering popular products/pages by rates
Gathering popular tags
Random product component
Recomend products by user visits
Pagination
Download page: increment downloads etc. @increments
(+) Order bills page: increment views etc. @increments
(+) Order Edit: communications, cancel, change (if changeable)
User Admin: logons_count @increments
Secret code for discounts: email + code; create new user
--------------------------------------------------------
Product page: groups only show products, but price should be set up manually
Product page: get attrs and consider them in prices (+ in cart)
Mixitup | Izotope для сортировки?
Model modifications on callme action
(+) Administrator notifications [email] if something went wrong
Routes: resourse construction: clean and remove unused methods to speed up
(+) User: administrator credentials
(+) Component: show / add to customers lists for logged and guest users
(+) Components: random product(s) ?
Exclude 'Main' category from results for attributes/tags pages etc.
(F) (web template) Login/logout/register/remember form
(F) Components: categories ?
Communications model inject
(A) Preloader Image
(D) Filter & search: configuration templates or redirect
Secret codes for pages & products ?
Categories' remote urls
When user press download on his order with digital downloads create temporary duplicate file in downloads table just for the session!
Out of stock products automatically change status to 'sold' or alert admin
Test if products with no categories are not showing!
SQLite: migration error: allows only one column drop at time

----------------------------------
comments

Initial memory usage: 6,237,040 ~ 7,820,848 | 7,817,704 | 7,125,344
Current: 9,691,208
* Is it necessary to split veerdb class?


[sidebar] categories
[sidebar] popular categories
[sidebar] popular products
[sidebar] random products
[sidebar] recomend products
[index] filters
[sidebar] one attribute with values
[page] attributes
[sidebar] banners
[sidebar] basket
[page] categories
[category] ratings on categories page
[category] recomends on categories page
[element] comments
[compare]
[sidebar] googlenews
[page] tags
[sidebar] lastnews
[page] list of all pages
[sidebar] popular tags
[product] product
[product] recomend same attr
[product] recomend same cat
[product] recomend same manuf
[product] recomend same tags
[sidebar] sapelinks
[order] delivery - ems, kuryer, pickpoint, ruspost, sam, spsr, autotr
[order] payment - nal, predoplata, qiwi, rbkcredit, rbkeuroset, rbkpostoffice, rbkterminal
[user] add customer (after registration)
[user] customers page -> gather user data
[user] guestbook - add callme, contact us etc
[user] login
[user] logout
[user] remember
forms - add 2 basket full with attrs
forms - add 2 list full
forms - basket form
forms - basket form address book
forms - basket form comments
forms - basket form delivery
forms - basket form patment
forms - basket form quickie
forms - callme form
forms - comment form
forms - contact us form
forms - login form
forms - logout form
forms - rate form
forms - register form
forms - review form
forsm - search form
image - captcha
actions - registering done
actions - login done
actions - logout done
actions - remember password done
actions - add2list done
actions - add2cart
actions - update cart
actions - review send done
actions - out of list done
actions - comment send done
actions - vote done
actions - callme send done
actions - guestbook send done
order - numbers in russian
order - addrbook
order - check basket selects
order - clear basket cookies
order - delivery address
order - delivery type
order - mail2all
order - make
order - make bills
order - payment type
order - pvcollect
order - read basket cookies
order - save basket cookies
order - show bill
order - show order
order - status finder
order - ymaps
page - add comment
user - register
user - basket
user - compare
user - contact
user - bills
user - order success
user - orders
user - customers page
products - add 2 list
products - add review
products - add review only rate
products - collect products
products - collect product
products - show basket
products - show basket collect info
products - compare
products - compare del
products - show basket attrs
products - show basket mix
products - show comments
products - sort products
products - update basket
templates - basket
templates - bill, 1,2,3
templates - comments
ymaps
-----------------------------------
Component: Lock access to some pages - depending on payment [payment wall]


category, attribute, filter, image, order, page, product, search, tag, user:
c a f i o p p s t u 
+ - - - - + + - - - sub/parent
+ + + - - + + + + - attribute
- - - - - - - - - + comment
- - - - - - - - - + communications ?
+ + + + + + + + + + component
- - - - - + + - - ? download
+ - - - - + + - - - image
+ + + + - + + + + - page
+ + + + + + + + + - product
+ + + + + + + + + + <site
+ + + - - + + + + - tag
+ + + + - + + + + - category
- - - - + - - - - + orders


!order - site, user, userbook, userdiscount, status, delivery, payment, status_history, products, bills
page -  comments, communications
product - !orders, comments, communications
!search
!user - site, role, comments, books, discounts, userlists, orders, bills, communications, administrator, searches, pages

-----

SHOP: orders_status, shipping, payment, discounts
 user roles*
      orders > orders bills, orders history*, orders products*

USERS: users, user admins* > user books, user lists, searches > user discounts*, user roles
			 > pages*, orders*
			 > comments, communications
		  					
SETTINGS: cache, migrations
	  password_reminders

TODO: yandex.market, 1s, xls (import/export)

Single pages: order, user, category, page, product!
? attribute

search!