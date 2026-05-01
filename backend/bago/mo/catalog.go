package mo

// Catalogs tabla catalogs
type Catalogs struct {
	ID       uint   `json:"id" gorm:"primary_key;type:int(11);auto_increment"`
	Cat      string `json:"cat" gorm:"type:varchar(45);not null"`
	Eti      string `json:"eti" gorm:"type:varchar(45);not null"`
	Val      string `json:"val" gorm:"type:varchar(254);not null"`
	NumOrder int    `json:"num_order" gorm:"type:tinyint(3);unsigned;not null;default:0"`
}

// GetCatalogs obtiene la lista de catálogos por categoría
func IntCatList(cat string) []Catalogs {
	var catList []Catalogs
	Dbl.Where("cat = ?", cat).Order("num_order").Find(&catList)

	return catList
}
