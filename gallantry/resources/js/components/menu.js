const Menu = {};
Menu.burger = {};
Menu.mobile = {};
Menu.desktop = {};
Menu.resizer = {};

//This function is run at page load and calls methods for the hamburger and the mobile menu.
Menu.init = function() {
  const breakpoint = 1024;
  const menu = document.querySelector('.main-menu');
  //Turn on the paths
  let pathArray = window.location.pathname.split('/');
  if (pathArray.length > 1) {
    let parent = pathArray[1];
    const options = menu.querySelectorAll('.main-menu__items >li');
    options.forEach(option=> {
      let link = option.querySelector('a');
      if (link.innerText.toLowerCase() == parent) {
        option.classList.add('parent');
      }
    });
  }
  const cta = document.querySelector('.letstalk');
  if (window.innerWidth > 768 && window.innerWidth < 1024) {
    Menu.resizer.reparent(menu,cta);
  }
  else {
    Menu.resizer.deparent(menu, cta);
  }



  /*
    THE MOBILE MENU
  */

  //We share this object with the mobile menu destory method so that we turn off the expanders when the menu is closed
  const burger = document.querySelector('.hamburger');
  const expanders = menu.querySelectorAll('.expander');

  //A click event listener on our expanders that triggers the open/close of the submenu on mobile viewports.
  expanders.forEach((expander => {
    expander.addEventListener('click',function() {
      let screenSize = window.innerWidth;
      if (screenSize < breakpoint) {
        let secondLevel = expander.previousElementSibling;
        if (expander.classList.contains('expander--on')) {
          Menu.mobile.secondLevelOff(secondLevel);
          // menu.firstElementChild.style.overflowY = 'hidden';
          // menu.firstElementChild.style.overflowX = 'initial';
          expander.classList.remove('expander--on');
        }
        else  {
          Menu.mobile.secondLevelOn(secondLevel);
          // menu.firstElementChild.style.overflowY = 'scroll'
          //   menu.firstElementChild.style.overflowX = 'hidden';
          expander.classList.add('expander--on');
        }
      }
    });
  }));

  //A click event listener on the Hamburger
  burger.addEventListener('click',function(e) {
    if (this.classList.contains('hamburger--on')) {
      this.classList.remove('hamburger--on');
      Menu.mobile.destory(menu,expanders);
    }
    else {
      this.classList.add('hamburger--on');
      Menu.mobile.init(menu);
    }
  });

  /*
    THE DESKTOP MENU
  */

  const parentMenuItems = document.querySelectorAll('.main-menu__items__item');
  parentMenuItems.forEach((parentMenuItem) => {
    const subNav = '' || parentMenuItem.children[1];
    parentMenuItem.addEventListener('mouseover',function(e) {
      let screenSize = window.innerWidth;
      if (screenSize > breakpoint) {
        if (parentMenuItem.classList.contains('main-menu__items__item--has-children')) {
          parentMenuItem.classList.add('hover');
          Menu.desktop.hoverOn(subNav);
        }
      }
    });

    parentMenuItem.addEventListener('touchstart',function(e) {
      let screenSize = window.innerWidth;
      if (screenSize > breakpoint) {
        if (parentMenuItem.classList.contains('main-menu__items__item--has-children')) {
          parentMenuItem.classList.add('hover');
          Menu.desktop.hoverOn(subNav);
        }
      }
    });

    parentMenuItem.addEventListener('touchmove',function(e) {
      let screenSize = window.innerWidth;
      if (screenSize > breakpoint) {
        if (parentMenuItem.classList.contains('main-menu__items__item--has-children')) {
          parentMenuItem.classList.remove('hover');
          Menu.desktop.hoverOff(subNav);
        }
      }
    });

    parentMenuItem.addEventListener('mouseleave',function(e) {
      let screenSize = window.innerWidth;
      if (screenSize > breakpoint) {
        if (parentMenuItem.classList.contains('main-menu__items__item--has-children')) {
          parentMenuItem.classList.remove('hover');
          Menu.desktop.hoverOff(subNav);
        }
      }
    });
  });


//Call the Resize Listener
let resizeEnd;
  window.addEventListener("resize",function() {
  let screenSize = window.innerWidth;
  Menu.resizer.init(menu,breakpoint,expanders,parentMenuItems,burger,screenSize);
  clearTimeout(resizeEnd);
  resizeEnd = setTimeout(function() {
    Menu.resizer.destroy(menu,breakpoint,screenSize);
  },500);
  });
};

Menu.desktop.hoverOn = function(item) {
  item.classList.add('second-level--on');
};

Menu.desktop.hoverOff = function(item) {
  item.classList.remove('second-level--on');
};


//Set the second level element height.
Menu.mobile.init = function(menu,expanders) {
  const top = window.pageYOffset;
  const body = document.querySelector('body');
  body.style.overflow = `hidden`;
  menu.classList.add('main-menu--on');

  let secondLevels = menu.querySelectorAll('.second-level');
  secondLevels.forEach((secondLevel) => {
    let height = secondLevel.getBoundingClientRect().height;
    secondLevel.dataset.height = height;
    if (height > 300) {
      secondLevel.dataset.height = 300;
    }
    else {
      secondLevel.dataset.height = height;
    }

  });
};

//Turn on and off the second menu when an expander is clicked.
Menu.mobile.secondLevelOn = function(secondLevel) {
  secondLevel.classList.add('second-level--on');
  // if (secondLevel.parentElement.nextElementSibling) {
  //   secondLevel.parentElement.nextElementSibling.style.marginTop = secondLevel.dataset.height;
  //
  // }
  // else {
  //   secondLevel.parentElement.parentElement.style.paddingBottom = secondLevel.dataset.height;
  // }
};

Menu.mobile.secondLevelOff = function(secondLevel) {
  secondLevel.classList.remove('second-level--on');
  if (secondLevel.parentElement.nextElementSibling) {
    secondLevel.parentElement.nextElementSibling.style.marginTop = 0;
  }
  else {
    secondLevel.parentElement.parentElement.style.paddingBottom = '';
  }
};

//Kill the mobile menu
Menu.mobile.destory = function(menu,expanders) {
  const body = document.querySelector("body");
  body.style.overflow = '';
  menu.classList.remove('main-menu--on');
  let secondLevels = menu.querySelectorAll('.second-level');
  expanders.forEach((expander) => {
    expander.classList.remove('expander--on');
  });
  secondLevels.forEach((secondLevel) => {
    secondLevel.classList.remove('second-level--on');
    if (secondLevel.parentElement.nextElementSibling) {
      secondLevel.parentElement.nextElementSibling.style.marginTop = 0;
    }
    else {
      secondLevel.parentElement.parentElement.style.paddingBottom = '';
    }
  });
};

//Menu Screen Size Events
Menu.resizer.init = function (menu,breakpoint,expanders,parentMenuItems,burger,screenSize) {
  const cta = document.querySelector('.letstalk');
  if (screenSize > 768  && screenSize < 1024 ) {
    Menu.resizer.reparent(menu,cta);
  }
  else {
    Menu.resizer.deparent(menu,cta);
  }

  if (screenSize < breakpoint) {
    menu.style.visibility = 'hidden';
  }
  else {
    menu.style.display = '';
    burger.classList.remove('hamburger--on');
    Menu.mobile.destory(menu,expanders);
  }
};
Menu.resizer.destroy = function (menu,expanders) {
  menu.style.visibility = '';

};

Menu.resizer.reparent = function(menu,cta) {
  const ul = menu.querySelector('ul');
  menu.appendChild(cta);
};

Menu.resizer.deparent = function(menu,cta) {
  const ul = menu.querySelector('ul');
  ul.appendChild(cta);
};



module.exports = Menu;
