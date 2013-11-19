LEETPC WordPress Source
======

Oooh shiney....
------

This repo contains the code (wordpress plugins and themes) that power LEETPC.com.au.  I have made this repo public for strange reasons including to impress potential employers.

Useful links
------

#### Internal
- www.leetpc.com.au
- www.leetpc.com.au/wp-admin
- www.leetpc.com.au/cpanel

#### External
- www.wordpress.org
- www.html5blank.com
- www.integratedweb.com.au

Plans, notes, etc.
-------

#### Order flow

1. **/choose -** User chooses a computer
2. **/customize/190380423 -** User selects options for a 'computer', when they click 'add to cart' the customized product is added to their cart
3. **/my-cart -** User can update quantities, remote items, or place their order
4. **/order -** The page where names are taken, and credit cards are processed
5. **/order/success -** The page shown to the lucky punters whom's payment has been accepted

#### Post types

- **product -** Product
- **component -** Product component (CPU, OS, etc)
- **cart -** Shopping cart
- **lineitem -** Shopping cart line item
- **invoice -** Customer invoice/receipt

#### Pages needed

- **/choose** Product selection
- **/contact-us** General contact
- **/customer-care** Customer care (contact)
- **/customize/*PRODUCT_NUMBER*** Customize product
- **/invoice/*INVOICE_NUMBER*** Invoice details
- **/my-cart** Customer's shopping cart
- **/product/*PRODUCT_NUMBER*** Product information
- **/order** Order form
- **/order/success** Order success

#### Designs / templates

- Main (wrapper/header/footer) template
- Home page
- Select type page
- Customize selection page
- Order page
- 404 page
- Content page
