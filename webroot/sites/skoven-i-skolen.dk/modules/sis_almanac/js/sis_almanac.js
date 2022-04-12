Drupal.behaviors.sisAlmanac = {
  attach: function(context, settings) {
    const almanacList = document.querySelectorAll('.almanac__list-item');
    console.log(almanacList);
    for (let i = 0; i < almanacList.length ; i++) {
      console.log(almanacList[i]);
    }
  }
};
