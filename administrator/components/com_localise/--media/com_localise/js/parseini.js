/* Simple parser for INI in media */
var INIParser = Editor.Parser = (function() {
  var tokenizeINI = (function() {
    function normal(source, setState) {
      var ch = source.next();
      if (ch == ';') {
        setState(inComment);
        return null;
      } else if (ch == '[') {
        setState(inGroup);
        return null;
      } else if (/[A-Z_\.]/.test(ch)) {
        setState(inIdentifier);
        return null;
      } else {
        setState(inError);
        return null;
      }
    }

    function inError(source,setState) {
      while (!source.endOfLine()) {
        var ch = source.next();
      }
      setState(normal);
      return "ini-error";    
    }
  
    function inComment(source, setState) {
      while (!source.endOfLine()) {
        var ch = source.next();
      }
      setState(normal);
      return "ini-comment";
    }

    function inGroup(source, setState) {
      while (!source.endOfLine()) {
        var ch=source.next();
        if (ch=="]")
        {
          setState(inEndGroup);
          return "ini-group";
        }
      }
      setState(normal);
      return "ini-error";
    }

    function inEndGroup(source, setState)
    {
      if (source.peek()==';')
      {
        setState(inComment);
        return null;
      }
      setState(normal);
      return null;
    }
  
    function inIdentifier(source, setState) {
      source.nextWhile(matcher(/[A-Z_\.\-0-9]/));
      setState(inEqual);
      return "ini-identifier";
    }

    function inEqual(source, setState) {
      var ch=source.next();
      if (ch=='=') {
        setState(inValue);
        return "ini-equal";
      }
      setState(inError);
      return null;
    }
  
    function inValue(source, setState) {
      if (source.equals("\"")) {
        source.next();
        setState(inString);
        return null;
      } else if (source.equals('_')) {
        setState(inConstant);
        return null;
      } else {
        setState(inError);
        return null;
      }
    }
  
    function inRestValue(source, setState) {
      var ch = source.next();
      if (ch == ';') {
        setState(inComment);
        return null;
      } else if (ch == "\"") {
        setState(inString);
        return null;
      } else if (ch == '_') {
        setState(inConstant);
        return null;
      } else {
        setState(inError);
        return null;
      }
    }
  
    function inConstant(source, setState) {
      source.nextWhile(matcher(/[_\w\d]/));
      var word = source.get();
      if (word=='_QQ_') {
        if (source.endOfLine()) {
          setState(normal);
        } else {
          setState(inRestValue);
        }
        return {style: "ini-constant", content: word};
      } else {
        setState(normal);
        return {style: "ini-error", content: word};
      }
    }

    function inString(source, setState) {
      while (!source.endOfLine()) {
        var ch = source.next();
        if (ch == "\"")
          break;
      }
      if (source.endOfLine()) {
        setState(normal);
      } else {
        setState(inRestValue);
      }
      return "ini-string";
    }

    return function(source, startState) {
      return tokenizer(source, startState || normal);
    };
  })();

  function indentINI() {
    return function(nextChars) {
      return 0;
    };
  }

  // This is a very simplistic parser -- since CSS does not really
  // nest, it works acceptably well, but some nicer colouroing could
  // be provided with a more complicated parser.
  function parseINI(source, basecolumn) {
    basecolumn = 0;
    var tokens = tokenizeINI(source);

    var iter = {
      next: function() {
        var token = tokens.next(), style = token.style, content = token.content;

        if (content == "\n") 
          token.indentation = indentINI();

        return token;
      },

      copy: function() {
        var _tokenState = tokens.state;
        return function(source) {
          tokens = tokenizeINI(source, _tokenState);
          return iter;
        };
      }
    };
    return iter;
  }

  return {make: parseINI};
})();