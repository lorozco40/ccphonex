package rutas

import "phonex/bago/ct"

var routesChat = Routes{
	// Rutas de chat
	Route{"GET", "/chat", ct.ChatList, "ChatList"},
	// Route{"GET", "/chat/:id", ct.ChatOne, "ChatOne"},
	Route{"POST", "/chat", ct.ChatAdd, "ChatAdd"},
	// Route{"PUT", "/chat/:id", ct.ChatUpd, "ChatUpd"},
	// Route{"DELETE", "/chat/:id", ct.ChatDel, "ChatDel"},
	// Route{"GET", "/chat/:id/def", ct.ChatDefList, "ChatDefList"},
	// Route{"GET", "/chat/:id/def/:did", ct.ChatDefOne, "ChatDefOne"},
	// Route{"POST", "/chat/:id/def", ct.ChatDefAdd, "ChatDefAdd"},
	// Route{"PUT", "/chat/:id/def/:did", ct.ChatDefUpd, "ChatDefUpd"},
	// Route{"DELETE", "/chat/:id/def/:did", ct.ChatDefDel, "ChatDefDel"},
	// Route{"GET", "/chat/:id/mensaje", ct.ChatMsgList, "ChatMsgList"},
	// Route{"GET", "/chat/:id/mensaje/:mid", ct.ChatMsgOne, "ChatMsgOne"},
	// Route{"POST", "/chat/:id/mensaje", ct.ChatMsgAdd, "ChatMsgAdd"},
	// Route{"PUT", "/chat/:id/mensaje/:mid", ct.ChatMsgUpd, "ChatMsgUpd"},
	// Route{"DELETE", "/chat/:id/mensaje/:mid", ct.ChatMsgDel, "ChatMsgDel"},
}
