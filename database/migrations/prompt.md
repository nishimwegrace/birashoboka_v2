1. the github repository is public : https://github.com/nishimwegrace/birashoboka_v2.git
2. We are updating and upgrading an existing static website to make it dynamic, you can read it on https://birashobokacenter.org/
3. we want to keep the identity of the brand(theme, colors, typography - bu for the typography, you can choose to make it more elegant)
4. we want the website to be responsive across all devices
5. below are the sections and what we want for each section
I. Header
We want the header to fill the whole width, be in 3 parts - logo, nav items, action(donate).
1. Logo: make sure the logo is visible and links to /
2. Navs: we will have:  Home, About, Programs with {Volets} sub-navs that uses a pop-down card when hovered that contains the {Volets} from the database and the design here will add a small details about the volet when hovered, show also some of it's {activities}, News, Gallery, {Partners} then {Contact}.
We won't support multilingual, but only english means don't add a language choice, 
3. Action: Show the Donate button

II. Banner: We want  to keep the texts in the overlay but for the banner image we will use the 10 recent {posts} images and display them in a carousel with controls but no dots.
III. About us: this section displays an imag of the representative picture and a small description of the About with a CTA Read more. We can show in a simple card the numbers stats ({students}, {partners}) this will open the About page.
IV. Latest post: This is new and we show 10 latest {posts} as the actual section News is presented with the same shape and format of card. Add below the View All CTA that open the news page
V. {Partners}: we only display images, and no cards to allow horizontal logo to be seen with a fixed height but width be relative with the image to make image visible. Keep them scrollable with auto scroll and controllers
VI. {Testimonials}: Display them as they are actually presented  but with a small description from {Testimonial.content}
VII. gallery and here we display images from posts, when clicked, opens the detiails of the page on posts/details
VIII. Contact section
IX. Footer Section and this will be like a footerless website as google: we show the logo, copyright and social links only

About page: /about
1. detaild about description
2. a section mission & vision
3. HVPM and it's details(static) as found in current version online
4. Team section: holds photos with name and positions. the images will be displayed in a circular shape not in card. make it there we will create a model in the API for it later
5. core values

Programs:
pages in programs are dynamic ex: /program/{volet.name}
Here we show at the top as a banner a carousel of images from {posts} related to that {volet}, below comes a section of name ,info and description of it. then we show in cards {services} from that {volet}
Below we show in {posts} style from Home some news related to the {volet} then footer

News: /news - this holds all {posts}, when there {inscription} en cours, be displayed on top: 
1. this wil have a banner with dynamic posts carousel,  
2. show {posts} in cards as we've done before and show all in infinite scroll with lazy loading downlading only those near to be viewed to save browser memory and network

/news/{post.id}: this show a post details with an aside with 5 recent related {posts} (from the same {volet}). these recent shown only image and in a row with title, date,


Gallery: this show the gallery (as we presented them on Home) in infinite scroll lazy loading 
When an image is clicked opens the related post details

Partners: /partners this shows the {partners}  with their details 
Contact: /contact shows the contact form and information - adress, contacts

Donate buttons goes to /contact

Note: 
1. when you see {} these are entities in our database
2. For some models such as {Post} we have some missing columns like images and for your information, each post will be able to hold an array of image_urls and a featured_image that will represent a {Post}
3. We have not yet created the entity for {Team} and we will name it {Member} with id, name, position, bio(nullable), avatar. 

4. The website is built in plain HTML+JS+tailwindcss
5. https requests are done with js fetch API
6. the frontend will be served in /public of the backend API directly


<!-- Addition -->
1. increase a bit the font-size for readability for texts
2. in the Header, as we have two institutions one inherited from another - Birashoboka from HVPM, we need to show both Logo in home, see how they can be placed, considering their relationship hierarchy
3. the info that is appearing on News for Apply now when there is an ongoing enrollment should be displayed on top of the banner (z-index) on Home page too. This from both with navigate to an Enrollment page /apply/{volet}/{activity}
4. Apply has an introduction and mainly a form that will create {students} and {inscription} as one instance of student belongs to one inscription too within a {campain}. 
5. create an admin dashboard with
- form to create posts, partners, volets, activities, members, campains, edit students, open and close compain, and full management of the application. Keep the identity.
- this dashboard allows the admin to view enrolled students in an organized manner and be able to export data tables. 
- also create a login screen for admin {user} that will be logged in only when authenticted. but now leave admin open for testing 


<!-- other addings -->

1. in Header, for logos, we need them to be visible, we can remove the texts and increase the logo sizes to be visible
2. in admin dashboard; add create and manage members
3. when creating compaign, allow the admin to specify himself Select Program & Vocational Trade (Volet & Activité) for a compaign to have from its creation the info of related volet/activity
4.the student will only fill in information related with him only. Email is optional, emergency contact and whatsapp number be removed, national_id is replaced by  nationality(input.text) and is required
the student creates {student} and {inscription} entity while admin creates {compaing} with related volet/activity

<!-- version 3 prompt -->
1. the website public is not responsive on devices - make sure the humburger button opens an off-canvas that pops from right and lists the navs.
2. use the AOS (animated on scroll) for content first appearence in the dom
3. in enrollmen form, remove the section 1. Select Program & Vocational Trade (Volet & Activité) from the student form for  this will be specified by the admin when creating the campain: 
"""
txt
Admission Campaign *

Gey here — Hey there (Buta)
Volet de Formation *

CRBN — Centre de Réhabilitation Birashoboka de Ngozi
Specific Vocational Trade / Activity *

Psychosocial Counseling & Therapy (One-on-one psychological support, safe listening groups, and trauma rehabilitation.)
Training Center Location *

CRBN Campus — Ngozi (Rusuguti)
Preferred Time Slot *

Morning Cohort (08:00 - 12:00)
2

""" will be provided when creating the campaign and student only sees those information already and fills in his info.
4. in admin, add option to create {member}-> for team members to display in about. Put this tab next to Partners in admin and provide a management page for it