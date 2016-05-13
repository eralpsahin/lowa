# Lokanta Waiter System
------
## Description
---
This is a term project for one of my courses (CS306) Which was actually supposed to be really simple thing, just to use wordpress's page formatting I am implementing this database project as a wordpress plugin. Lowa is a Restaurant Waiter System Plugin to make it easier to operate a Restaurant by storing most of the information in an online database.

## Features
---
* Adding menu to the Restaurant
* Adding Ingredient information to Database
* Searching for a given Ingredient
* Adding newly arrived groups to the Restaurant, system automatically finds the appropriate table by the number of people in the arrived group
* Adding order for a group
* System keeps the statistics about the Restaurant
* Menu List, can be ordered by any attribute of the menu

All of the operations done asynchronously with JQuery and AJAX

## Shortcodes
All of the forms can be inserted by WordPress Shortcode. They are;

1.  `[Lowa-Add-Menu]` Adding Menu to System Form
2.  `[Lowa-Ingredient]` Search menus for an Ingredient Form
3.  `[Lowa-Add-Group]` Seat Groups to the Restaurant
4.  `[Lowa-Add-Ingredient]` Add Ingredients to Menus
5.  `[Lowa-Statistics]` Statistics for the Restaurant
6.  `[Lowa-Menu]` Menu list of the Restaurant
7.  `[Lowa-Have-Check]` Closes a Group's Account
8.  `[Lowa-Add-Order]` Adds order to a Group's Check



