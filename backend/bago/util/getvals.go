package util

import (
	"math/rand"
	"strconv"
	"strings"
	"time"
)

func Str2Date(s string) time.Time {
	if s == "" {
		return time.Time{}
	}
	t, e := time.Parse("2006-01-02", s)
	CheckErr(e)
	return t
}

func Str2Int(s string) int {
	i, e := strconv.Atoi(s)
	CheckErr(e)
	return i
}

func Str2Uint(s string) uint {
	return uint(Str2Int(s))
}

func IfaceToUint(i interface{}) uint {
	switch v := i.(type) {
	case int:
		return uint(v)
	case int8:
		return uint(v)
	case int16:
		return uint(v)
	case int32:
		return uint(v)
	case int64:
		return uint(v)
	case uint:
		return v
	case uint8:
		return uint(v)
	case uint16:
		return uint(v)
	case uint32:
		return uint(v)
	case uint64:
		return uint(v)
	case float32:
		return uint(v)
	case float64:
		return uint(v)
	case string:
		return Str2Uint(v)
	case bool:
		if v {
			return 1
		}
		return 0
	default:
		return 0
	}
}

func RandStringBytesMaskImpr(n int) string {
	const letterBytes = "abcdefghijklmnopqrstuvwxyz1234567890"
	const (
		letterIdxBits = 6                    // 6 bits to represent a letter index
		letterIdxMask = 1<<letterIdxBits - 1 // All 1-bits, as many as letterIdxBits
		letterIdxMax  = 63 / letterIdxBits   // # of letter indices fitting in 63 bits
	)
	b := make([]byte, n)
	// A rand.Int63() generates 63 random bits, enough for letterIdxMax letters!
	for i, cache, remain := n-1, rand.Int63(), letterIdxMax; i >= 0; {
		if remain == 0 {
			cache, remain = rand.Int63(), letterIdxMax
		}
		if idx := int(cache & letterIdxMask); idx < len(letterBytes) {
			b[i] = letterBytes[idx]
			i--
		}
		cache >>= letterIdxBits
		remain--
	}

	return string(b)
}

func Unixtime2Human(t int64) string {
	return time.Unix(t, 0).Format("2006-01-02 15:04:05")
}

func ComaSep2UintSlice(s string) []uint {
	var ret []uint
	valores := strings.Split(s, ",")
	for _, v := range valores {
		ret = append(ret, Str2Uint(v))
	}

	return ret
}
