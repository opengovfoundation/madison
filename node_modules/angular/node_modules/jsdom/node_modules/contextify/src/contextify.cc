#include "node.h"
#include "node_version.h"
#include "nan.h"
#include <string>
using namespace v8;
using namespace node;

// For some reason this needs to be out of the object or node won't load the
// library.
static Persistent<FunctionTemplate> dataWrapperTmpl;
static Persistent<Function>         dataWrapperCtor;

class ContextifyContext : public ObjectWrap {
public:
    Persistent<Context> context;
    Persistent<Object>  sandbox;
    Persistent<Object>  proxyGlobal;

    static Persistent<FunctionTemplate> jsTmpl;

    ContextifyContext(Local<Object> sbox) {
        NanScope();
        NanAssignPersistent(Object, sandbox, sbox);
    }

    ~ContextifyContext() {
        context.Dispose();
        context.Clear();
        proxyGlobal.Dispose();
        proxyGlobal.Clear();
        sandbox.Dispose();
        sandbox.Clear();
    }

    // We override ObjectWrap::Wrap so that we can create our context after
    // we have a reference to our "host" JavaScript object.  If we try to use
    // handle_ in the ContextifyContext constructor, it will be empty since it's
    // set in ObjectWrap::Wrap.
    inline void Wrap(Handle<Object> handle) {
        ObjectWrap::Wrap(handle);
        Local<Context> lcontext = createV8Context();
        NanAssignPersistent(Context, context, lcontext);
        NanAssignPersistent(Object, proxyGlobal, lcontext->Global());
    }

    // This is an object that just keeps an internal pointer to this
    // ContextifyContext.  It's passed to the NamedPropertyHandler.  If we
    // pass the main JavaScript context object we're embedded in, then the
    // NamedPropertyHandler will store a reference to it forever and keep it
    // from getting gc'd.
    Local<Object> createDataWrapper () {
        NanScope();
        Local<Object> wrapper = NanPersistentToLocal(dataWrapperCtor)->NewInstance();
        NanSetInternalFieldPointer(wrapper, 0, this);
        return scope.Close(wrapper);
    }

    inline Local<Context> createV8Context() {
        Local<FunctionTemplate> ftmpl = FunctionTemplate::New();
        ftmpl->SetHiddenPrototype(true);
        ftmpl->SetClassName(NanPersistentToLocal(sandbox)->GetConstructorName());
        Local<ObjectTemplate> otmpl = ftmpl->InstanceTemplate();
        otmpl->SetNamedPropertyHandler(GlobalPropertyGetter,
                                       GlobalPropertySetter,
                                       GlobalPropertyQuery,
                                       GlobalPropertyDeleter,
                                       GlobalPropertyEnumerator,
                                       createDataWrapper());
        otmpl->SetAccessCheckCallbacks(GlobalPropertyNamedAccessCheck,
                                       GlobalPropertyIndexedAccessCheck);
        return NanNewContextHandle(NULL, otmpl);
    }

    static void Init(Handle<Object> target) {
        NanScope();
        Local<FunctionTemplate> tmpl = NanNewLocal<FunctionTemplate>(FunctionTemplate::New());
        tmpl->InstanceTemplate()->SetInternalFieldCount(1);
        NanAssignPersistent(FunctionTemplate, dataWrapperTmpl, tmpl);
        NanAssignPersistent(Function, dataWrapperCtor, tmpl->GetFunction());

        Local<FunctionTemplate> ljsTmpl = NanNewLocal<FunctionTemplate>(FunctionTemplate::New(New));
        ljsTmpl->InstanceTemplate()->SetInternalFieldCount(1);
        ljsTmpl->SetClassName(String::NewSymbol("ContextifyContext"));
        NODE_SET_PROTOTYPE_METHOD(ljsTmpl, "run",       ContextifyContext::Run);
        NODE_SET_PROTOTYPE_METHOD(ljsTmpl, "getGlobal", ContextifyContext::GetGlobal);

        NanAssignPersistent(FunctionTemplate, jsTmpl, ljsTmpl);
        target->Set(String::NewSymbol("ContextifyContext"), ljsTmpl->GetFunction());
    }

    static NAN_METHOD(New) {
        NanScope();

        if (args.Length() < 1) {
            Local<String> msg = String::New("Wrong number of arguments passed to ContextifyContext constructor");
            NanReturnValue(ThrowException(Exception::Error(msg)));
        }

        if (!args[0]->IsObject()) {
            Local<String> msg = String::New("Argument to ContextifyContext constructor must be an object.");
            NanReturnValue(ThrowException(Exception::Error(msg)));
        }

        ContextifyContext* ctx = new ContextifyContext(args[0]->ToObject());
        ctx->Wrap(args.This());
        NanReturnValue(args.This());
    }

    static NAN_METHOD(Run) {
        NanScope();
        if (args.Length() == 0) {
            Local<String> msg = String::New("Must supply at least 1 argument to run");
            NanReturnValue(ThrowException(Exception::Error(msg)));
        }
        if (!args[0]->IsString()) {
            Local<String> msg = String::New("First argument to run must be a String.");
            NanReturnValue(ThrowException(Exception::Error(msg)));
        }
        ContextifyContext* ctx = ObjectWrap::Unwrap<ContextifyContext>(args.This());
        Persistent<Context> context;
        Local<Context> lcontext = NanPersistentToLocal(ctx->context);
        NanAssignPersistent(Context, context, lcontext);
        lcontext->Enter();
        Local<String> code = args[0]->ToString();

        TryCatch trycatch;
        Handle<Script> script;

        if (args.Length() > 1 && args[1]->IsString()) {
            script = Script::Compile(code, args[1]->ToString());
        } else {
            script = Script::Compile(code);
        }

        if (script.IsEmpty()) {
          lcontext->Exit();
          NanReturnValue(trycatch.ReThrow());
        }

        Handle<Value> result = script->Run();
        lcontext->Exit();

        if (result.IsEmpty()) {
            NanReturnValue(trycatch.ReThrow());
        }

        NanReturnValue(result);
    }

    static bool InstanceOf(Handle<Value> value) {
      return NanHasInstance(jsTmpl, value);
    }

    static NAN_METHOD(GetGlobal) {
        NanScope();
        ContextifyContext* ctx = ObjectWrap::Unwrap<ContextifyContext>(args.This());
        NanReturnValue(ctx->proxyGlobal);
    }

    static bool GlobalPropertyNamedAccessCheck(Local<Object> host,
                                               Local<Value>  key,
                                               AccessType    type,
                                               Local<Value>  data) {
        return true;
    }

    static bool GlobalPropertyIndexedAccessCheck(Local<Object> host,
                                                 uint32_t      key,
                                                 AccessType    type,
                                                 Local<Value>  data) {
        return true;
    }

    static NAN_PROPERTY_GETTER(GlobalPropertyGetter) {
        NanScope();
        Local<Object> data = args.Data()->ToObject();
        ContextifyContext* ctx = ObjectWrap::Unwrap<ContextifyContext>(data);
        Local<Value> rv = NanPersistentToLocal(ctx->sandbox)->GetRealNamedProperty(property);
        if (rv.IsEmpty()) {
            rv = NanPersistentToLocal(ctx->proxyGlobal)->GetRealNamedProperty(property);
        }
        NanReturnValue(rv);
    }

    static NAN_PROPERTY_SETTER(GlobalPropertySetter) {
        NanScope();
        Local<Object> data = args.Data()->ToObject();
        ContextifyContext* ctx = ObjectWrap::Unwrap<ContextifyContext>(data);
        NanPersistentToLocal(ctx->sandbox)->Set(property, value);
        NanReturnValue(value);
    }

    static NAN_PROPERTY_QUERY(GlobalPropertyQuery) {
        NanScope();
        Local<Object> data = args.Data()->ToObject();
        ContextifyContext* ctx = ObjectWrap::Unwrap<ContextifyContext>(data);
        if (!NanPersistentToLocal(ctx->sandbox)->GetRealNamedProperty(property).IsEmpty() ||
            !NanPersistentToLocal(ctx->proxyGlobal)->GetRealNamedProperty(property).IsEmpty()) {
            NanReturnValue(Integer::New(None));
         } else {
            NanReturnValue(Handle<Integer>());
         }
    }

    static NAN_PROPERTY_DELETER(GlobalPropertyDeleter) {
        NanScope();
        Local<Object> data = args.Data()->ToObject();
        ContextifyContext* ctx = ObjectWrap::Unwrap<ContextifyContext>(data);
        bool success = NanPersistentToLocal(ctx->sandbox)->Delete(property);
        NanReturnValue(Boolean::New(success));
    }

    static NAN_PROPERTY_ENUMERATOR(GlobalPropertyEnumerator) {
        NanScope();
        Local<Object> data = args.Data()->ToObject();
        ContextifyContext* ctx = ObjectWrap::Unwrap<ContextifyContext>(data);
        NanReturnValue(NanPersistentToLocal(ctx->sandbox)->GetPropertyNames());
    }
};

class ContextifyScript : public ObjectWrap {
public:
    static Persistent<FunctionTemplate> scriptTmpl;
    Persistent<Script> script;

    static void Init(Handle<Object> target) {
        NanScope();
        Local<FunctionTemplate> lscriptTmpl = NanNewLocal<FunctionTemplate>(FunctionTemplate::New(New));
        lscriptTmpl->InstanceTemplate()->SetInternalFieldCount(1);
        lscriptTmpl->SetClassName(String::NewSymbol("ContextifyScript"));

        NODE_SET_PROTOTYPE_METHOD(lscriptTmpl, "runInContext", RunInContext);

        NanAssignPersistent(FunctionTemplate, scriptTmpl, lscriptTmpl);
        target->Set(String::NewSymbol("ContextifyScript"),
                    lscriptTmpl->GetFunction());
    }
    static NAN_METHOD(New) {
        NanScope();
        ContextifyScript *contextify_script = new ContextifyScript();
        contextify_script->Wrap(args.Holder());

        if (args.Length() < 1) {
          NanReturnValue(ThrowException(Exception::TypeError(
            String::New("needs at least 'code' argument."))));
        }

        Local<String> code = args[0]->ToString();
        Local<String> filename = args.Length() > 1
                               ? args[1]->ToString()
                               : String::New("ContextifyScript.<anonymous>");

        Handle<Context> context = Context::GetCurrent();
        Context::Scope context_scope(context);

        // Catch errors
        TryCatch trycatch;

        Handle<Script> v8_script = Script::New(code, filename);

        if (v8_script.IsEmpty()) {
          NanReturnValue(trycatch.ReThrow());
        }

        NanAssignPersistent(Script, contextify_script->script, v8_script);

        NanReturnValue(args.This());
    }

    static NAN_METHOD(RunInContext) {
        NanScope();
        if (args.Length() == 0) {
            Local<String> msg = String::New("Must supply at least 1 argument to runInContext");
            NanReturnValue(ThrowException(Exception::Error(msg)));
        }
        if (!ContextifyContext::InstanceOf(args[0]->ToObject())) {
            Local<String> msg = String::New("First argument must be a ContextifyContext.");
            NanReturnValue(ThrowException(Exception::TypeError(msg)));
        }

        ContextifyContext* ctx = ObjectWrap::Unwrap<ContextifyContext>(args[0]->ToObject());
        Local<Context> lcontext = NanPersistentToLocal(ctx->context);
        Persistent<Context> context;
        NanAssignPersistent(Context, context, lcontext);
        lcontext->Enter();
        ContextifyScript* wrapped_script = ObjectWrap::Unwrap<ContextifyScript>(args.This());
        Handle<Script> script = NanPersistentToLocal(wrapped_script->script);
        TryCatch trycatch;
        if (script.IsEmpty()) {
          lcontext->Exit();
          NanReturnValue(trycatch.ReThrow());
        }
        Handle<Value> result = script->Run();
        lcontext->Exit();
        if (result.IsEmpty()) {
            NanReturnValue(trycatch.ReThrow());
        }
        NanReturnValue(result);
    }

    ~ContextifyScript() {
        script.Dispose();
    }
};

Persistent<FunctionTemplate> ContextifyContext::jsTmpl;
Persistent<FunctionTemplate> ContextifyScript::scriptTmpl;

extern "C" {
    static void init(Handle<Object> target) {
        ContextifyContext::Init(target);
        ContextifyScript::Init(target);
    }
    NODE_MODULE(contextify, init);
};
