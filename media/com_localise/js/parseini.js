CodeMirror.defineMode("parseini", function() {
	return {
		token: function(stream, state) {
			var sol = stream.sol() || state.afterSection;
			var eol = stream.eol();

			state.afterSection = false;

			if (sol) {
				if (state.nextMultiline) {
					state.inMultiline = true;
					state.nextMultiline = false;
				} else {
					state.position = "identifier";
				}
			}

			if (eol && ! state.nextMultiline) {
				state.inMultiline = false;
				state.position = "identifier";
			}

			var ch = stream.next();

			if (sol && (ch === ";"))
			{
				state.position = "comment";
				stream.skipToEnd();
				return "comment";
			}
			else if (sol && ch === "[")
			{
				state.afterSection = true;
				stream.skipTo("]"); stream.eat("]");
				return "group";
			}
			else if (sol && /[A-Z_\.]/.test(ch) && state.position === 'identifier')
			{
				stream.eatWhile(/[A-Z_\*\.\-0-9]/);
				state.position = "equal";
				blacklist = ["YES", "NO", "NULL", "FALSE", "ON", "OFF", "NONE", "TRUE"];
				if(blacklist.indexOf(stream.current()) > -1)
				{
					return "error";
				}
				return "identifier";
			}
			else if (!sol && ch === "=" && state.position === "equal")
			{
				state.position = "string";
				return "equal";
			}
			else if (ch === '"' && state.position === "string")
			{
				state.position = "string";

				while(stream.skipTo('"'))
				{
					if (stream.eol())
					{
						return 'string';
					}

					if (stream.string.charAt(stream.pos-1) == '\\')
					{
						stream.pos++;
						continue;
					}

					stream.eat('"');
					return 'string';
				}
				return 'error';
			}
			else if (ch === '_' && state.position === "string")
			{
				if (stream.match('QQ_'))
				{
					state.position = "string";
					return 'constant';
				}
				return "error";
			}
			else
			{
				return "error";
			}
		},

		startState: function() {
			return {
				position : "identifier",       // Current position, "identifier", "string" or "comment"
				nextMultiline : false,  // Is the next line multiline value
				inMultiline : false,    // Is the current line a multiline value
				afterSection : false    // Did we just open a section
			};
		}

	};
});

CodeMirror.defineMIME("text/parseini", "parseini");