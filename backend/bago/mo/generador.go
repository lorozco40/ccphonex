//go:build generate

package mo

import (
    "gorm.io/gen"
)

func genModelos() {
    g := gen.NewGenerator(gen.Config{
        OutPath: "./query",
        Mode:    gen.WithoutContext | gen.WithDefaultQuery | gen.WithQueryInterface, // generate mode
    })
    g.UseDB(Dbl)
    g.ApplyBasic(
        g.GenerateModel("disp_field"),
    )
    g.Execute()
}
