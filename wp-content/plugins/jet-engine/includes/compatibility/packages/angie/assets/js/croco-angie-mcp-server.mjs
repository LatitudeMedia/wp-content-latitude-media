var Yo = Object.defineProperty;
var Xo = (s, e, r) => e in s ? Yo(s, e, { enumerable: !0, configurable: !0, writable: !0, value: r }) : s[e] = r;
var Xe = (s, e, r) => Xo(s, typeof e != "symbol" ? e + "" : e, r);
var hs, Er, Re, bn;
(function(s) {
  s.POST_MESSAGE = "postMessage";
})(hs || (hs = {})), function(s) {
  s.SDK_ANGIE_READY_PING = "sdk-angie-ready-ping", s.SDK_REQUEST_CLIENT_CREATION = "sdk-request-client-creation", s.SDK_REQUEST_INIT_SERVER = "sdk-request-init-server";
}(Er || (Er = {}));
class eu {
  constructor() {
    Xe(this, "isAngieReady", !1);
    Xe(this, "readyPromise");
    Xe(this, "readyResolve");
    if (this.readyPromise = new Promise((a) => {
      this.readyResolve = a;
    }), typeof window > "u") return;
    let e = 0;
    const r = () => {
      if (this.isAngieReady || e >= 500) return void (!this.isAngieReady && e >= 500 && this.handleDetectionTimeout());
      const a = new MessageChannel();
      a.port1.onmessage = (n) => {
        this.handleAngieReady(n.data), a.port1.close(), a.port2.close();
      };
      const t = { type: Er.SDK_ANGIE_READY_PING, timestamp: Date.now() };
      window.postMessage(t, window.location.origin, [a.port2]), e++, setTimeout(r, 500);
    };
    r();
  }
  handleAngieReady(e) {
    this.isAngieReady = !0;
    const r = { isReady: !0, version: e.version, capabilities: e.capabilities };
    this.readyResolve && this.readyResolve(r);
  }
  handleDetectionTimeout() {
    this.readyResolve && this.readyResolve({ isReady: !1 }), console.warn("AngieMcpSdk: AngieDetector: Detection timeout - Angie may not be available");
  }
  isReady() {
    return this.isAngieReady;
  }
  async waitForReady() {
    return this.readyPromise;
  }
}
class tu {
  constructor() {
    Xe(this, "queue", []);
    Xe(this, "isProcessing", !1);
  }
  add(e) {
    const r = { id: this.generateId(e), config: e, timestamp: Date.now(), status: "pending" };
    return this.queue.push(r), console.log(`RegistrationQueue: Added server "${e.name}" to queue`), r;
  }
  getAll() {
    return [...this.queue];
  }
  getPending() {
    return this.queue.filter((e) => e.status === "pending");
  }
  updateStatus(e, r, a) {
    const t = this.queue.find((n) => n.id === e);
    t && (t.status = r, a && (t.error = a), console.log(`RegistrationQueue: Updated server ${e} status to ${r}`));
  }
  async processQueue(e) {
    if (this.isProcessing) return void console.log("RegistrationQueue: Already processing queue");
    this.isProcessing = !0;
    const r = this.getPending();
    console.log(`RegistrationQueue: Processing ${r.length} pending registrations`);
    try {
      for (const a of r) try {
        await e(a), this.updateStatus(a.id, "registered");
      } catch (t) {
        const n = t instanceof Error ? t.message : String(t);
        this.updateStatus(a.id, "failed", n), console.error(`RegistrationQueue: Failed to process registration ${a.id}:`, n);
      }
    } finally {
      this.isProcessing = !1;
    }
  }
  clear() {
    this.queue = [], console.log("RegistrationQueue: Cleared all registrations");
  }
  remove(e) {
    const r = this.queue.findIndex((a) => a.id === e);
    return r !== -1 && (this.queue.splice(r, 1), console.log(`RegistrationQueue: Removed registration ${e}`), !0);
  }
  generateId(e) {
    return `reg_${e.name}_${e.version}_${Date.now()}`;
  }
}
class ru {
  async requestClientCreation(e) {
    const { config: r } = e, a = { serverId: e.id, serverName: r.name, serverVersion: r.version, description: r.description, transport: hs.POST_MESSAGE, capabilities: r.capabilities };
    return new Promise((t, n) => {
      const i = new MessageChannel(), o = setTimeout(() => {
        n(new Error("Client creation request timed out after 10000ms"));
      }, 1e4);
      i.port1.onmessage = (l) => {
        clearTimeout(o), t(l.data);
      };
      const u = { type: Er.SDK_REQUEST_CLIENT_CREATION, payload: a, timestamp: Date.now() };
      window.postMessage(u, window.location.origin, [i.port2]);
    });
  }
}
(function(s) {
  s.assertEqual = (e) => {
  }, s.assertIs = function(e) {
  }, s.assertNever = function(e) {
    throw new Error();
  }, s.arrayToEnum = (e) => {
    const r = {};
    for (const a of e) r[a] = a;
    return r;
  }, s.getValidEnumValues = (e) => {
    const r = s.objectKeys(e).filter((t) => typeof e[e[t]] != "number"), a = {};
    for (const t of r) a[t] = e[t];
    return s.objectValues(a);
  }, s.objectValues = (e) => s.objectKeys(e).map(function(r) {
    return e[r];
  }), s.objectKeys = typeof Object.keys == "function" ? (e) => Object.keys(e) : (e) => {
    const r = [];
    for (const a in e) Object.prototype.hasOwnProperty.call(e, a) && r.push(a);
    return r;
  }, s.find = (e, r) => {
    for (const a of e) if (r(a)) return a;
  }, s.isInteger = typeof Number.isInteger == "function" ? (e) => Number.isInteger(e) : (e) => typeof e == "number" && Number.isFinite(e) && Math.floor(e) === e, s.joinValues = function(e, r = " | ") {
    return e.map((a) => typeof a == "string" ? `'${a}'` : a).join(r);
  }, s.jsonStringifyReplacer = (e, r) => typeof r == "bigint" ? r.toString() : r;
})(Re || (Re = {})), function(s) {
  s.mergeShapes = (e, r) => ({ ...e, ...r });
}(bn || (bn = {}));
const G = Re.arrayToEnum(["string", "nan", "number", "integer", "float", "boolean", "date", "bigint", "symbol", "function", "undefined", "null", "array", "object", "unknown", "promise", "void", "never", "map", "set"]), Jt = (s) => {
  switch (typeof s) {
    case "undefined":
      return G.undefined;
    case "string":
      return G.string;
    case "number":
      return Number.isNaN(s) ? G.nan : G.number;
    case "boolean":
      return G.boolean;
    case "function":
      return G.function;
    case "bigint":
      return G.bigint;
    case "symbol":
      return G.symbol;
    case "object":
      return Array.isArray(s) ? G.array : s === null ? G.null : s.then && typeof s.then == "function" && s.catch && typeof s.catch == "function" ? G.promise : typeof Map < "u" && s instanceof Map ? G.map : typeof Set < "u" && s instanceof Set ? G.set : typeof Date < "u" && s instanceof Date ? G.date : G.object;
    default:
      return G.unknown;
  }
}, M = Re.arrayToEnum(["invalid_type", "invalid_literal", "custom", "invalid_union", "invalid_union_discriminator", "invalid_enum_value", "unrecognized_keys", "invalid_arguments", "invalid_return_type", "invalid_date", "invalid_string", "too_small", "too_big", "invalid_intersection_types", "not_multiple_of", "not_finite"]);
class Vt extends Error {
  get errors() {
    return this.issues;
  }
  constructor(e) {
    super(), this.issues = [], this.addIssue = (a) => {
      this.issues = [...this.issues, a];
    }, this.addIssues = (a = []) => {
      this.issues = [...this.issues, ...a];
    };
    const r = new.target.prototype;
    Object.setPrototypeOf ? Object.setPrototypeOf(this, r) : this.__proto__ = r, this.name = "ZodError", this.issues = e;
  }
  format(e) {
    const r = e || function(n) {
      return n.message;
    }, a = { _errors: [] }, t = (n) => {
      for (const i of n.issues) if (i.code === "invalid_union") i.unionErrors.map(t);
      else if (i.code === "invalid_return_type") t(i.returnTypeError);
      else if (i.code === "invalid_arguments") t(i.argumentsError);
      else if (i.path.length === 0) a._errors.push(r(i));
      else {
        let o = a, u = 0;
        for (; u < i.path.length; ) {
          const l = i.path[u];
          u === i.path.length - 1 ? (o[l] = o[l] || { _errors: [] }, o[l]._errors.push(r(i))) : o[l] = o[l] || { _errors: [] }, o = o[l], u++;
        }
      }
    };
    return t(this), a;
  }
  static assert(e) {
    if (!(e instanceof Vt)) throw new Error(`Not a ZodError: ${e}`);
  }
  toString() {
    return this.message;
  }
  get message() {
    return JSON.stringify(this.issues, Re.jsonStringifyReplacer, 2);
  }
  get isEmpty() {
    return this.issues.length === 0;
  }
  flatten(e = (r) => r.message) {
    const r = {}, a = [];
    for (const t of this.issues) t.path.length > 0 ? (r[t.path[0]] = r[t.path[0]] || [], r[t.path[0]].push(e(t))) : a.push(e(t));
    return { formErrors: a, fieldErrors: r };
  }
  get formErrors() {
    return this.flatten();
  }
}
Vt.create = (s) => new Vt(s);
const fs = (s, e) => {
  let r;
  switch (s.code) {
    case M.invalid_type:
      r = s.received === G.undefined ? "Required" : `Expected ${s.expected}, received ${s.received}`;
      break;
    case M.invalid_literal:
      r = `Invalid literal value, expected ${JSON.stringify(s.expected, Re.jsonStringifyReplacer)}`;
      break;
    case M.unrecognized_keys:
      r = `Unrecognized key(s) in object: ${Re.joinValues(s.keys, ", ")}`;
      break;
    case M.invalid_union:
      r = "Invalid input";
      break;
    case M.invalid_union_discriminator:
      r = `Invalid discriminator value. Expected ${Re.joinValues(s.options)}`;
      break;
    case M.invalid_enum_value:
      r = `Invalid enum value. Expected ${Re.joinValues(s.options)}, received '${s.received}'`;
      break;
    case M.invalid_arguments:
      r = "Invalid function arguments";
      break;
    case M.invalid_return_type:
      r = "Invalid function return type";
      break;
    case M.invalid_date:
      r = "Invalid date";
      break;
    case M.invalid_string:
      typeof s.validation == "object" ? "includes" in s.validation ? (r = `Invalid input: must include "${s.validation.includes}"`, typeof s.validation.position == "number" && (r = `${r} at one or more positions greater than or equal to ${s.validation.position}`)) : "startsWith" in s.validation ? r = `Invalid input: must start with "${s.validation.startsWith}"` : "endsWith" in s.validation ? r = `Invalid input: must end with "${s.validation.endsWith}"` : Re.assertNever(s.validation) : r = s.validation !== "regex" ? `Invalid ${s.validation}` : "Invalid";
      break;
    case M.too_small:
      r = s.type === "array" ? `Array must contain ${s.exact ? "exactly" : s.inclusive ? "at least" : "more than"} ${s.minimum} element(s)` : s.type === "string" ? `String must contain ${s.exact ? "exactly" : s.inclusive ? "at least" : "over"} ${s.minimum} character(s)` : s.type === "number" ? `Number must be ${s.exact ? "exactly equal to " : s.inclusive ? "greater than or equal to " : "greater than "}${s.minimum}` : s.type === "date" ? `Date must be ${s.exact ? "exactly equal to " : s.inclusive ? "greater than or equal to " : "greater than "}${new Date(Number(s.minimum))}` : "Invalid input";
      break;
    case M.too_big:
      r = s.type === "array" ? `Array must contain ${s.exact ? "exactly" : s.inclusive ? "at most" : "less than"} ${s.maximum} element(s)` : s.type === "string" ? `String must contain ${s.exact ? "exactly" : s.inclusive ? "at most" : "under"} ${s.maximum} character(s)` : s.type === "number" ? `Number must be ${s.exact ? "exactly" : s.inclusive ? "less than or equal to" : "less than"} ${s.maximum}` : s.type === "bigint" ? `BigInt must be ${s.exact ? "exactly" : s.inclusive ? "less than or equal to" : "less than"} ${s.maximum}` : s.type === "date" ? `Date must be ${s.exact ? "exactly" : s.inclusive ? "smaller than or equal to" : "smaller than"} ${new Date(Number(s.maximum))}` : "Invalid input";
      break;
    case M.custom:
      r = "Invalid input";
      break;
    case M.invalid_intersection_types:
      r = "Intersection results could not be merged";
      break;
    case M.not_multiple_of:
      r = `Number must be a multiple of ${s.multipleOf}`;
      break;
    case M.not_finite:
      r = "Number must be finite";
      break;
    default:
      r = e.defaultError, Re.assertNever(s);
  }
  return { message: r };
};
let au = fs;
function Q(s, e) {
  const r = au, a = ((t) => {
    const { data: n, path: i, errorMaps: o, issueData: u } = t, l = [...i, ...u.path || []], h = { ...u, path: l };
    if (u.message !== void 0) return { ...u, path: l, message: u.message };
    let m = "";
    const _ = o.filter((c) => !!c).slice().reverse();
    for (const c of _) m = c(h, { data: n, defaultError: m }).message;
    return { ...u, path: l, message: m };
  })({ issueData: e, data: s.data, path: s.path, errorMaps: [s.common.contextualErrorMap, s.schemaErrorMap, r, r === fs ? void 0 : fs].filter((t) => !!t) });
  s.common.issues.push(a);
}
class it {
  constructor() {
    this.value = "valid";
  }
  dirty() {
    this.value === "valid" && (this.value = "dirty");
  }
  abort() {
    this.value !== "aborted" && (this.value = "aborted");
  }
  static mergeArray(e, r) {
    const a = [];
    for (const t of r) {
      if (t.status === "aborted") return ie;
      t.status === "dirty" && e.dirty(), a.push(t.value);
    }
    return { status: e.value, value: a };
  }
  static async mergeObjectAsync(e, r) {
    const a = [];
    for (const t of r) {
      const n = await t.key, i = await t.value;
      a.push({ key: n, value: i });
    }
    return it.mergeObjectSync(e, a);
  }
  static mergeObjectSync(e, r) {
    const a = {};
    for (const t of r) {
      const { key: n, value: i } = t;
      if (n.status === "aborted" || i.status === "aborted") return ie;
      n.status === "dirty" && e.dirty(), i.status === "dirty" && e.dirty(), n.value === "__proto__" || i.value === void 0 && !t.alwaysSet || (a[n.value] = i.value);
    }
    return { status: e.value, value: a };
  }
}
const ie = Object.freeze({ status: "aborted" }), ps = (s) => ({ status: "dirty", value: s }), gt = (s) => ({ status: "valid", value: s }), Pn = (s) => s.status === "aborted", Sn = (s) => s.status === "dirty", fr = (s) => s.status === "valid", zr = (s) => typeof Promise < "u" && s instanceof Promise;
var ee;
(function(s) {
  s.errToObj = (e) => typeof e == "string" ? { message: e } : e || {}, s.toString = (e) => typeof e == "string" ? e : e == null ? void 0 : e.message;
})(ee || (ee = {}));
class $t {
  constructor(e, r, a, t) {
    this._cachedPath = [], this.parent = e, this.data = r, this._path = a, this._key = t;
  }
  get path() {
    return this._cachedPath.length || (Array.isArray(this._key) ? this._cachedPath.push(...this._path, ...this._key) : this._cachedPath.push(...this._path, this._key)), this._cachedPath;
  }
}
const wn = (s, e) => {
  if (fr(e)) return { success: !0, data: e.value };
  if (!s.common.issues.length) throw new Error("Validation failed but no issues detected.");
  return { success: !1, get error() {
    if (this._error) return this._error;
    const r = new Vt(s.common.issues);
    return this._error = r, this._error;
  } };
};
function ge(s) {
  if (!s) return {};
  const { errorMap: e, invalid_type_error: r, required_error: a, description: t } = s;
  if (e && (r || a)) throw new Error(`Can't use "invalid_type_error" or "required_error" in conjunction with custom error map.`);
  return e ? { errorMap: e, description: t } : { errorMap: (n, i) => {
    const { message: o } = s;
    return n.code === "invalid_enum_value" ? { message: o ?? i.defaultError } : i.data === void 0 ? { message: o ?? a ?? i.defaultError } : n.code !== "invalid_type" ? { message: i.defaultError } : { message: o ?? r ?? i.defaultError };
  }, description: t };
}
class xe {
  get description() {
    return this._def.description;
  }
  _getType(e) {
    return Jt(e.data);
  }
  _getOrReturnCtx(e, r) {
    return r || { common: e.parent.common, data: e.data, parsedType: Jt(e.data), schemaErrorMap: this._def.errorMap, path: e.path, parent: e.parent };
  }
  _processInputParams(e) {
    return { status: new it(), ctx: { common: e.parent.common, data: e.data, parsedType: Jt(e.data), schemaErrorMap: this._def.errorMap, path: e.path, parent: e.parent } };
  }
  _parseSync(e) {
    const r = this._parse(e);
    if (zr(r)) throw new Error("Synchronous parse encountered promise.");
    return r;
  }
  _parseAsync(e) {
    const r = this._parse(e);
    return Promise.resolve(r);
  }
  parse(e, r) {
    const a = this.safeParse(e, r);
    if (a.success) return a.data;
    throw a.error;
  }
  safeParse(e, r) {
    const a = { common: { issues: [], async: (r == null ? void 0 : r.async) ?? !1, contextualErrorMap: r == null ? void 0 : r.errorMap }, path: (r == null ? void 0 : r.path) || [], schemaErrorMap: this._def.errorMap, parent: null, data: e, parsedType: Jt(e) }, t = this._parseSync({ data: e, path: a.path, parent: a });
    return wn(a, t);
  }
  "~validate"(e) {
    var a, t;
    const r = { common: { issues: [], async: !!this["~standard"].async }, path: [], schemaErrorMap: this._def.errorMap, parent: null, data: e, parsedType: Jt(e) };
    if (!this["~standard"].async) try {
      const n = this._parseSync({ data: e, path: [], parent: r });
      return fr(n) ? { value: n.value } : { issues: r.common.issues };
    } catch (n) {
      (t = (a = n == null ? void 0 : n.message) == null ? void 0 : a.toLowerCase()) != null && t.includes("encountered") && (this["~standard"].async = !0), r.common = { issues: [], async: !0 };
    }
    return this._parseAsync({ data: e, path: [], parent: r }).then((n) => fr(n) ? { value: n.value } : { issues: r.common.issues });
  }
  async parseAsync(e, r) {
    const a = await this.safeParseAsync(e, r);
    if (a.success) return a.data;
    throw a.error;
  }
  async safeParseAsync(e, r) {
    const a = { common: { issues: [], contextualErrorMap: r == null ? void 0 : r.errorMap, async: !0 }, path: (r == null ? void 0 : r.path) || [], schemaErrorMap: this._def.errorMap, parent: null, data: e, parsedType: Jt(e) }, t = this._parse({ data: e, path: a.path, parent: a }), n = await (zr(t) ? t : Promise.resolve(t));
    return wn(a, n);
  }
  refine(e, r) {
    const a = (t) => typeof r == "string" || r === void 0 ? { message: r } : typeof r == "function" ? r(t) : r;
    return this._refinement((t, n) => {
      const i = e(t), o = () => n.addIssue({ code: M.custom, ...a(t) });
      return typeof Promise < "u" && i instanceof Promise ? i.then((u) => !!u || (o(), !1)) : !!i || (o(), !1);
    });
  }
  refinement(e, r) {
    return this._refinement((a, t) => !!e(a) || (t.addIssue(typeof r == "function" ? r(a, t) : r), !1));
  }
  _refinement(e) {
    return new tr({ schema: this, typeName: ue.ZodEffects, effect: { type: "refinement", refinement: e } });
  }
  superRefine(e) {
    return this._refinement(e);
  }
  constructor(e) {
    this.spa = this.safeParseAsync, this._def = e, this.parse = this.parse.bind(this), this.safeParse = this.safeParse.bind(this), this.parseAsync = this.parseAsync.bind(this), this.safeParseAsync = this.safeParseAsync.bind(this), this.spa = this.spa.bind(this), this.refine = this.refine.bind(this), this.refinement = this.refinement.bind(this), this.superRefine = this.superRefine.bind(this), this.optional = this.optional.bind(this), this.nullable = this.nullable.bind(this), this.nullish = this.nullish.bind(this), this.array = this.array.bind(this), this.promise = this.promise.bind(this), this.or = this.or.bind(this), this.and = this.and.bind(this), this.transform = this.transform.bind(this), this.brand = this.brand.bind(this), this.default = this.default.bind(this), this.catch = this.catch.bind(this), this.describe = this.describe.bind(this), this.pipe = this.pipe.bind(this), this.readonly = this.readonly.bind(this), this.isNullable = this.isNullable.bind(this), this.isOptional = this.isOptional.bind(this), this["~standard"] = { version: 1, vendor: "zod", validate: (r) => this["~validate"](r) };
  }
  optional() {
    return zt.create(this, this._def);
  }
  nullable() {
    return sr.create(this, this._def);
  }
  nullish() {
    return this.nullable().optional();
  }
  array() {
    return Ot.create(this);
  }
  promise() {
    return Qr.create(this, this._def);
  }
  or(e) {
    return Vr.create([this, e], this._def);
  }
  and(e) {
    return Hr.create(this, e, this._def);
  }
  transform(e) {
    return new tr({ ...ge(this._def), schema: this, typeName: ue.ZodEffects, effect: { type: "transform", transform: e } });
  }
  default(e) {
    const r = typeof e == "function" ? e : () => e;
    return new Kr({ ...ge(this._def), innerType: this, defaultValue: r, typeName: ue.ZodDefault });
  }
  brand() {
    return new Bi({ typeName: ue.ZodBranded, type: this, ...ge(this._def) });
  }
  catch(e) {
    const r = typeof e == "function" ? e : () => e;
    return new Jr({ ...ge(this._def), innerType: this, catchValue: r, typeName: ue.ZodCatch });
  }
  describe(e) {
    return new this.constructor({ ...this._def, description: e });
  }
  pipe(e) {
    return Us.create(this, e);
  }
  readonly() {
    return Wr.create(this);
  }
  isOptional() {
    return this.safeParse(void 0).success;
  }
  isNullable() {
    return this.safeParse(null).success;
  }
}
const su = /^c[^\s-]{8,}$/i, nu = /^[0-9a-z]+$/, iu = /^[0-9A-HJKMNP-TV-Z]{26}$/i, ou = /^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$/i, uu = /^[a-z0-9_-]{21}$/i, lu = /^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]*$/, cu = /^[-+]?P(?!$)(?:(?:[-+]?\d+Y)|(?:[-+]?\d+[.,]\d+Y$))?(?:(?:[-+]?\d+M)|(?:[-+]?\d+[.,]\d+M$))?(?:(?:[-+]?\d+W)|(?:[-+]?\d+[.,]\d+W$))?(?:(?:[-+]?\d+D)|(?:[-+]?\d+[.,]\d+D$))?(?:T(?=[\d+-])(?:(?:[-+]?\d+H)|(?:[-+]?\d+[.,]\d+H$))?(?:(?:[-+]?\d+M)|(?:[-+]?\d+[.,]\d+M$))?(?:[-+]?\d+(?:[.,]\d+)?S)?)??$/, du = /^(?!\.)(?!.*\.\.)([A-Z0-9_'+\-\.]*)[A-Z0-9_+-]@([A-Z0-9][A-Z0-9\-]*\.)+[A-Z]{2,}$/i;
let _a;
const hu = /^(?:(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])\.){3}(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])$/, fu = /^(?:(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])\.){3}(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])\/(3[0-2]|[12]?[0-9])$/, pu = /^(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))$/, mu = /^(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))\/(12[0-8]|1[01][0-9]|[1-9]?[0-9])$/, vu = /^([0-9a-zA-Z+/]{4})*(([0-9a-zA-Z+/]{2}==)|([0-9a-zA-Z+/]{3}=))?$/, gu = /^([0-9a-zA-Z-_]{4})*(([0-9a-zA-Z-_]{2}(==)?)|([0-9a-zA-Z-_]{3}(=)?))?$/, Ui = "((\\d\\d[2468][048]|\\d\\d[13579][26]|\\d\\d0[48]|[02468][048]00|[13579][26]00)-02-29|\\d{4}-((0[13578]|1[02])-(0[1-9]|[12]\\d|3[01])|(0[469]|11)-(0[1-9]|[12]\\d|30)|(02)-(0[1-9]|1\\d|2[0-8])))", yu = new RegExp(`^${Ui}$`);
function Vi(s) {
  let e = "[0-5]\\d";
  return s.precision ? e = `${e}\\.\\d{${s.precision}}` : s.precision == null && (e = `${e}(\\.\\d+)?`), `([01]\\d|2[0-3]):[0-5]\\d(:${e})${s.precision ? "+" : "?"}`;
}
function _u(s) {
  let e = `${Ui}T${Vi(s)}`;
  const r = [];
  return r.push(s.local ? "Z?" : "Z"), s.offset && r.push("([+-]\\d{2}:?\\d{2})"), e = `${e}(${r.join("|")})`, new RegExp(`^${e}$`);
}
function bu(s, e) {
  if (!lu.test(s)) return !1;
  try {
    const [r] = s.split("."), a = r.replace(/-/g, "+").replace(/_/g, "/").padEnd(r.length + (4 - r.length % 4) % 4, "="), t = JSON.parse(atob(a));
    return !(typeof t != "object" || t === null || "typ" in t && (t == null ? void 0 : t.typ) !== "JWT" || !t.alg || e && t.alg !== e);
  } catch {
    return !1;
  }
}
function Pu(s, e) {
  return !(e !== "v4" && e || !fu.test(s)) || !(e !== "v6" && e || !mu.test(s));
}
class Zt extends xe {
  _parse(e) {
    if (this._def.coerce && (e.data = String(e.data)), this._getType(e) !== G.string) {
      const i = this._getOrReturnCtx(e);
      return Q(i, { code: M.invalid_type, expected: G.string, received: i.parsedType }), ie;
    }
    const r = new it();
    let a;
    for (const i of this._def.checks) if (i.kind === "min") e.data.length < i.value && (a = this._getOrReturnCtx(e, a), Q(a, { code: M.too_small, minimum: i.value, type: "string", inclusive: !0, exact: !1, message: i.message }), r.dirty());
    else if (i.kind === "max") e.data.length > i.value && (a = this._getOrReturnCtx(e, a), Q(a, { code: M.too_big, maximum: i.value, type: "string", inclusive: !0, exact: !1, message: i.message }), r.dirty());
    else if (i.kind === "length") {
      const o = e.data.length > i.value, u = e.data.length < i.value;
      (o || u) && (a = this._getOrReturnCtx(e, a), o ? Q(a, { code: M.too_big, maximum: i.value, type: "string", inclusive: !0, exact: !0, message: i.message }) : u && Q(a, { code: M.too_small, minimum: i.value, type: "string", inclusive: !0, exact: !0, message: i.message }), r.dirty());
    } else if (i.kind === "email") du.test(e.data) || (a = this._getOrReturnCtx(e, a), Q(a, { validation: "email", code: M.invalid_string, message: i.message }), r.dirty());
    else if (i.kind === "emoji") _a || (_a = new RegExp("^(\\p{Extended_Pictographic}|\\p{Emoji_Component})+$", "u")), _a.test(e.data) || (a = this._getOrReturnCtx(e, a), Q(a, { validation: "emoji", code: M.invalid_string, message: i.message }), r.dirty());
    else if (i.kind === "uuid") ou.test(e.data) || (a = this._getOrReturnCtx(e, a), Q(a, { validation: "uuid", code: M.invalid_string, message: i.message }), r.dirty());
    else if (i.kind === "nanoid") uu.test(e.data) || (a = this._getOrReturnCtx(e, a), Q(a, { validation: "nanoid", code: M.invalid_string, message: i.message }), r.dirty());
    else if (i.kind === "cuid") su.test(e.data) || (a = this._getOrReturnCtx(e, a), Q(a, { validation: "cuid", code: M.invalid_string, message: i.message }), r.dirty());
    else if (i.kind === "cuid2") nu.test(e.data) || (a = this._getOrReturnCtx(e, a), Q(a, { validation: "cuid2", code: M.invalid_string, message: i.message }), r.dirty());
    else if (i.kind === "ulid") iu.test(e.data) || (a = this._getOrReturnCtx(e, a), Q(a, { validation: "ulid", code: M.invalid_string, message: i.message }), r.dirty());
    else if (i.kind === "url") try {
      new URL(e.data);
    } catch {
      a = this._getOrReturnCtx(e, a), Q(a, { validation: "url", code: M.invalid_string, message: i.message }), r.dirty();
    }
    else i.kind === "regex" ? (i.regex.lastIndex = 0, i.regex.test(e.data) || (a = this._getOrReturnCtx(e, a), Q(a, { validation: "regex", code: M.invalid_string, message: i.message }), r.dirty())) : i.kind === "trim" ? e.data = e.data.trim() : i.kind === "includes" ? e.data.includes(i.value, i.position) || (a = this._getOrReturnCtx(e, a), Q(a, { code: M.invalid_string, validation: { includes: i.value, position: i.position }, message: i.message }), r.dirty()) : i.kind === "toLowerCase" ? e.data = e.data.toLowerCase() : i.kind === "toUpperCase" ? e.data = e.data.toUpperCase() : i.kind === "startsWith" ? e.data.startsWith(i.value) || (a = this._getOrReturnCtx(e, a), Q(a, { code: M.invalid_string, validation: { startsWith: i.value }, message: i.message }), r.dirty()) : i.kind === "endsWith" ? e.data.endsWith(i.value) || (a = this._getOrReturnCtx(e, a), Q(a, { code: M.invalid_string, validation: { endsWith: i.value }, message: i.message }), r.dirty()) : i.kind === "datetime" ? _u(i).test(e.data) || (a = this._getOrReturnCtx(e, a), Q(a, { code: M.invalid_string, validation: "datetime", message: i.message }), r.dirty()) : i.kind === "date" ? yu.test(e.data) || (a = this._getOrReturnCtx(e, a), Q(a, { code: M.invalid_string, validation: "date", message: i.message }), r.dirty()) : i.kind === "time" ? new RegExp(`^${Vi(i)}$`).test(e.data) || (a = this._getOrReturnCtx(e, a), Q(a, { code: M.invalid_string, validation: "time", message: i.message }), r.dirty()) : i.kind === "duration" ? cu.test(e.data) || (a = this._getOrReturnCtx(e, a), Q(a, { validation: "duration", code: M.invalid_string, message: i.message }), r.dirty()) : i.kind === "ip" ? (t = e.data, ((n = i.version) !== "v4" && n || !hu.test(t)) && (n !== "v6" && n || !pu.test(t)) && (a = this._getOrReturnCtx(e, a), Q(a, { validation: "ip", code: M.invalid_string, message: i.message }), r.dirty())) : i.kind === "jwt" ? bu(e.data, i.alg) || (a = this._getOrReturnCtx(e, a), Q(a, { validation: "jwt", code: M.invalid_string, message: i.message }), r.dirty()) : i.kind === "cidr" ? Pu(e.data, i.version) || (a = this._getOrReturnCtx(e, a), Q(a, { validation: "cidr", code: M.invalid_string, message: i.message }), r.dirty()) : i.kind === "base64" ? vu.test(e.data) || (a = this._getOrReturnCtx(e, a), Q(a, { validation: "base64", code: M.invalid_string, message: i.message }), r.dirty()) : i.kind === "base64url" ? gu.test(e.data) || (a = this._getOrReturnCtx(e, a), Q(a, { validation: "base64url", code: M.invalid_string, message: i.message }), r.dirty()) : Re.assertNever(i);
    var t, n;
    return { status: r.value, value: e.data };
  }
  _regex(e, r, a) {
    return this.refinement((t) => e.test(t), { validation: r, code: M.invalid_string, ...ee.errToObj(a) });
  }
  _addCheck(e) {
    return new Zt({ ...this._def, checks: [...this._def.checks, e] });
  }
  email(e) {
    return this._addCheck({ kind: "email", ...ee.errToObj(e) });
  }
  url(e) {
    return this._addCheck({ kind: "url", ...ee.errToObj(e) });
  }
  emoji(e) {
    return this._addCheck({ kind: "emoji", ...ee.errToObj(e) });
  }
  uuid(e) {
    return this._addCheck({ kind: "uuid", ...ee.errToObj(e) });
  }
  nanoid(e) {
    return this._addCheck({ kind: "nanoid", ...ee.errToObj(e) });
  }
  cuid(e) {
    return this._addCheck({ kind: "cuid", ...ee.errToObj(e) });
  }
  cuid2(e) {
    return this._addCheck({ kind: "cuid2", ...ee.errToObj(e) });
  }
  ulid(e) {
    return this._addCheck({ kind: "ulid", ...ee.errToObj(e) });
  }
  base64(e) {
    return this._addCheck({ kind: "base64", ...ee.errToObj(e) });
  }
  base64url(e) {
    return this._addCheck({ kind: "base64url", ...ee.errToObj(e) });
  }
  jwt(e) {
    return this._addCheck({ kind: "jwt", ...ee.errToObj(e) });
  }
  ip(e) {
    return this._addCheck({ kind: "ip", ...ee.errToObj(e) });
  }
  cidr(e) {
    return this._addCheck({ kind: "cidr", ...ee.errToObj(e) });
  }
  datetime(e) {
    return typeof e == "string" ? this._addCheck({ kind: "datetime", precision: null, offset: !1, local: !1, message: e }) : this._addCheck({ kind: "datetime", precision: (e == null ? void 0 : e.precision) === void 0 ? null : e == null ? void 0 : e.precision, offset: (e == null ? void 0 : e.offset) ?? !1, local: (e == null ? void 0 : e.local) ?? !1, ...ee.errToObj(e == null ? void 0 : e.message) });
  }
  date(e) {
    return this._addCheck({ kind: "date", message: e });
  }
  time(e) {
    return typeof e == "string" ? this._addCheck({ kind: "time", precision: null, message: e }) : this._addCheck({ kind: "time", precision: (e == null ? void 0 : e.precision) === void 0 ? null : e == null ? void 0 : e.precision, ...ee.errToObj(e == null ? void 0 : e.message) });
  }
  duration(e) {
    return this._addCheck({ kind: "duration", ...ee.errToObj(e) });
  }
  regex(e, r) {
    return this._addCheck({ kind: "regex", regex: e, ...ee.errToObj(r) });
  }
  includes(e, r) {
    return this._addCheck({ kind: "includes", value: e, position: r == null ? void 0 : r.position, ...ee.errToObj(r == null ? void 0 : r.message) });
  }
  startsWith(e, r) {
    return this._addCheck({ kind: "startsWith", value: e, ...ee.errToObj(r) });
  }
  endsWith(e, r) {
    return this._addCheck({ kind: "endsWith", value: e, ...ee.errToObj(r) });
  }
  min(e, r) {
    return this._addCheck({ kind: "min", value: e, ...ee.errToObj(r) });
  }
  max(e, r) {
    return this._addCheck({ kind: "max", value: e, ...ee.errToObj(r) });
  }
  length(e, r) {
    return this._addCheck({ kind: "length", value: e, ...ee.errToObj(r) });
  }
  nonempty(e) {
    return this.min(1, ee.errToObj(e));
  }
  trim() {
    return new Zt({ ...this._def, checks: [...this._def.checks, { kind: "trim" }] });
  }
  toLowerCase() {
    return new Zt({ ...this._def, checks: [...this._def.checks, { kind: "toLowerCase" }] });
  }
  toUpperCase() {
    return new Zt({ ...this._def, checks: [...this._def.checks, { kind: "toUpperCase" }] });
  }
  get isDatetime() {
    return !!this._def.checks.find((e) => e.kind === "datetime");
  }
  get isDate() {
    return !!this._def.checks.find((e) => e.kind === "date");
  }
  get isTime() {
    return !!this._def.checks.find((e) => e.kind === "time");
  }
  get isDuration() {
    return !!this._def.checks.find((e) => e.kind === "duration");
  }
  get isEmail() {
    return !!this._def.checks.find((e) => e.kind === "email");
  }
  get isURL() {
    return !!this._def.checks.find((e) => e.kind === "url");
  }
  get isEmoji() {
    return !!this._def.checks.find((e) => e.kind === "emoji");
  }
  get isUUID() {
    return !!this._def.checks.find((e) => e.kind === "uuid");
  }
  get isNANOID() {
    return !!this._def.checks.find((e) => e.kind === "nanoid");
  }
  get isCUID() {
    return !!this._def.checks.find((e) => e.kind === "cuid");
  }
  get isCUID2() {
    return !!this._def.checks.find((e) => e.kind === "cuid2");
  }
  get isULID() {
    return !!this._def.checks.find((e) => e.kind === "ulid");
  }
  get isIP() {
    return !!this._def.checks.find((e) => e.kind === "ip");
  }
  get isCIDR() {
    return !!this._def.checks.find((e) => e.kind === "cidr");
  }
  get isBase64() {
    return !!this._def.checks.find((e) => e.kind === "base64");
  }
  get isBase64url() {
    return !!this._def.checks.find((e) => e.kind === "base64url");
  }
  get minLength() {
    let e = null;
    for (const r of this._def.checks) r.kind === "min" && (e === null || r.value > e) && (e = r.value);
    return e;
  }
  get maxLength() {
    let e = null;
    for (const r of this._def.checks) r.kind === "max" && (e === null || r.value < e) && (e = r.value);
    return e;
  }
}
function Su(s, e) {
  const r = (s.toString().split(".")[1] || "").length, a = (e.toString().split(".")[1] || "").length, t = r > a ? r : a;
  return Number.parseInt(s.toFixed(t).replace(".", "")) % Number.parseInt(e.toFixed(t).replace(".", "")) / 10 ** t;
}
Zt.create = (s) => new Zt({ checks: [], typeName: ue.ZodString, coerce: (s == null ? void 0 : s.coerce) ?? !1, ...ge(s) });
class pr extends xe {
  constructor() {
    super(...arguments), this.min = this.gte, this.max = this.lte, this.step = this.multipleOf;
  }
  _parse(e) {
    if (this._def.coerce && (e.data = Number(e.data)), this._getType(e) !== G.number) {
      const t = this._getOrReturnCtx(e);
      return Q(t, { code: M.invalid_type, expected: G.number, received: t.parsedType }), ie;
    }
    let r;
    const a = new it();
    for (const t of this._def.checks) t.kind === "int" ? Re.isInteger(e.data) || (r = this._getOrReturnCtx(e, r), Q(r, { code: M.invalid_type, expected: "integer", received: "float", message: t.message }), a.dirty()) : t.kind === "min" ? (t.inclusive ? e.data < t.value : e.data <= t.value) && (r = this._getOrReturnCtx(e, r), Q(r, { code: M.too_small, minimum: t.value, type: "number", inclusive: t.inclusive, exact: !1, message: t.message }), a.dirty()) : t.kind === "max" ? (t.inclusive ? e.data > t.value : e.data >= t.value) && (r = this._getOrReturnCtx(e, r), Q(r, { code: M.too_big, maximum: t.value, type: "number", inclusive: t.inclusive, exact: !1, message: t.message }), a.dirty()) : t.kind === "multipleOf" ? Su(e.data, t.value) !== 0 && (r = this._getOrReturnCtx(e, r), Q(r, { code: M.not_multiple_of, multipleOf: t.value, message: t.message }), a.dirty()) : t.kind === "finite" ? Number.isFinite(e.data) || (r = this._getOrReturnCtx(e, r), Q(r, { code: M.not_finite, message: t.message }), a.dirty()) : Re.assertNever(t);
    return { status: a.value, value: e.data };
  }
  gte(e, r) {
    return this.setLimit("min", e, !0, ee.toString(r));
  }
  gt(e, r) {
    return this.setLimit("min", e, !1, ee.toString(r));
  }
  lte(e, r) {
    return this.setLimit("max", e, !0, ee.toString(r));
  }
  lt(e, r) {
    return this.setLimit("max", e, !1, ee.toString(r));
  }
  setLimit(e, r, a, t) {
    return new pr({ ...this._def, checks: [...this._def.checks, { kind: e, value: r, inclusive: a, message: ee.toString(t) }] });
  }
  _addCheck(e) {
    return new pr({ ...this._def, checks: [...this._def.checks, e] });
  }
  int(e) {
    return this._addCheck({ kind: "int", message: ee.toString(e) });
  }
  positive(e) {
    return this._addCheck({ kind: "min", value: 0, inclusive: !1, message: ee.toString(e) });
  }
  negative(e) {
    return this._addCheck({ kind: "max", value: 0, inclusive: !1, message: ee.toString(e) });
  }
  nonpositive(e) {
    return this._addCheck({ kind: "max", value: 0, inclusive: !0, message: ee.toString(e) });
  }
  nonnegative(e) {
    return this._addCheck({ kind: "min", value: 0, inclusive: !0, message: ee.toString(e) });
  }
  multipleOf(e, r) {
    return this._addCheck({ kind: "multipleOf", value: e, message: ee.toString(r) });
  }
  finite(e) {
    return this._addCheck({ kind: "finite", message: ee.toString(e) });
  }
  safe(e) {
    return this._addCheck({ kind: "min", inclusive: !0, value: Number.MIN_SAFE_INTEGER, message: ee.toString(e) })._addCheck({ kind: "max", inclusive: !0, value: Number.MAX_SAFE_INTEGER, message: ee.toString(e) });
  }
  get minValue() {
    let e = null;
    for (const r of this._def.checks) r.kind === "min" && (e === null || r.value > e) && (e = r.value);
    return e;
  }
  get maxValue() {
    let e = null;
    for (const r of this._def.checks) r.kind === "max" && (e === null || r.value < e) && (e = r.value);
    return e;
  }
  get isInt() {
    return !!this._def.checks.find((e) => e.kind === "int" || e.kind === "multipleOf" && Re.isInteger(e.value));
  }
  get isFinite() {
    let e = null, r = null;
    for (const a of this._def.checks) {
      if (a.kind === "finite" || a.kind === "int" || a.kind === "multipleOf") return !0;
      a.kind === "min" ? (r === null || a.value > r) && (r = a.value) : a.kind === "max" && (e === null || a.value < e) && (e = a.value);
    }
    return Number.isFinite(r) && Number.isFinite(e);
  }
}
pr.create = (s) => new pr({ checks: [], typeName: ue.ZodNumber, coerce: (s == null ? void 0 : s.coerce) || !1, ...ge(s) });
class Rr extends xe {
  constructor() {
    super(...arguments), this.min = this.gte, this.max = this.lte;
  }
  _parse(e) {
    if (this._def.coerce) try {
      e.data = BigInt(e.data);
    } catch {
      return this._getInvalidInput(e);
    }
    if (this._getType(e) !== G.bigint) return this._getInvalidInput(e);
    let r;
    const a = new it();
    for (const t of this._def.checks) t.kind === "min" ? (t.inclusive ? e.data < t.value : e.data <= t.value) && (r = this._getOrReturnCtx(e, r), Q(r, { code: M.too_small, type: "bigint", minimum: t.value, inclusive: t.inclusive, message: t.message }), a.dirty()) : t.kind === "max" ? (t.inclusive ? e.data > t.value : e.data >= t.value) && (r = this._getOrReturnCtx(e, r), Q(r, { code: M.too_big, type: "bigint", maximum: t.value, inclusive: t.inclusive, message: t.message }), a.dirty()) : t.kind === "multipleOf" ? e.data % t.value !== BigInt(0) && (r = this._getOrReturnCtx(e, r), Q(r, { code: M.not_multiple_of, multipleOf: t.value, message: t.message }), a.dirty()) : Re.assertNever(t);
    return { status: a.value, value: e.data };
  }
  _getInvalidInput(e) {
    const r = this._getOrReturnCtx(e);
    return Q(r, { code: M.invalid_type, expected: G.bigint, received: r.parsedType }), ie;
  }
  gte(e, r) {
    return this.setLimit("min", e, !0, ee.toString(r));
  }
  gt(e, r) {
    return this.setLimit("min", e, !1, ee.toString(r));
  }
  lte(e, r) {
    return this.setLimit("max", e, !0, ee.toString(r));
  }
  lt(e, r) {
    return this.setLimit("max", e, !1, ee.toString(r));
  }
  setLimit(e, r, a, t) {
    return new Rr({ ...this._def, checks: [...this._def.checks, { kind: e, value: r, inclusive: a, message: ee.toString(t) }] });
  }
  _addCheck(e) {
    return new Rr({ ...this._def, checks: [...this._def.checks, e] });
  }
  positive(e) {
    return this._addCheck({ kind: "min", value: BigInt(0), inclusive: !1, message: ee.toString(e) });
  }
  negative(e) {
    return this._addCheck({ kind: "max", value: BigInt(0), inclusive: !1, message: ee.toString(e) });
  }
  nonpositive(e) {
    return this._addCheck({ kind: "max", value: BigInt(0), inclusive: !0, message: ee.toString(e) });
  }
  nonnegative(e) {
    return this._addCheck({ kind: "min", value: BigInt(0), inclusive: !0, message: ee.toString(e) });
  }
  multipleOf(e, r) {
    return this._addCheck({ kind: "multipleOf", value: e, message: ee.toString(r) });
  }
  get minValue() {
    let e = null;
    for (const r of this._def.checks) r.kind === "min" && (e === null || r.value > e) && (e = r.value);
    return e;
  }
  get maxValue() {
    let e = null;
    for (const r of this._def.checks) r.kind === "max" && (e === null || r.value < e) && (e = r.value);
    return e;
  }
}
Rr.create = (s) => new Rr({ checks: [], typeName: ue.ZodBigInt, coerce: (s == null ? void 0 : s.coerce) ?? !1, ...ge(s) });
class ms extends xe {
  _parse(e) {
    if (this._def.coerce && (e.data = !!e.data), this._getType(e) !== G.boolean) {
      const r = this._getOrReturnCtx(e);
      return Q(r, { code: M.invalid_type, expected: G.boolean, received: r.parsedType }), ie;
    }
    return gt(e.data);
  }
}
ms.create = (s) => new ms({ typeName: ue.ZodBoolean, coerce: (s == null ? void 0 : s.coerce) || !1, ...ge(s) });
class Ur extends xe {
  _parse(e) {
    if (this._def.coerce && (e.data = new Date(e.data)), this._getType(e) !== G.date) {
      const t = this._getOrReturnCtx(e);
      return Q(t, { code: M.invalid_type, expected: G.date, received: t.parsedType }), ie;
    }
    if (Number.isNaN(e.data.getTime())) return Q(this._getOrReturnCtx(e), { code: M.invalid_date }), ie;
    const r = new it();
    let a;
    for (const t of this._def.checks) t.kind === "min" ? e.data.getTime() < t.value && (a = this._getOrReturnCtx(e, a), Q(a, { code: M.too_small, message: t.message, inclusive: !0, exact: !1, minimum: t.value, type: "date" }), r.dirty()) : t.kind === "max" ? e.data.getTime() > t.value && (a = this._getOrReturnCtx(e, a), Q(a, { code: M.too_big, message: t.message, inclusive: !0, exact: !1, maximum: t.value, type: "date" }), r.dirty()) : Re.assertNever(t);
    return { status: r.value, value: new Date(e.data.getTime()) };
  }
  _addCheck(e) {
    return new Ur({ ...this._def, checks: [...this._def.checks, e] });
  }
  min(e, r) {
    return this._addCheck({ kind: "min", value: e.getTime(), message: ee.toString(r) });
  }
  max(e, r) {
    return this._addCheck({ kind: "max", value: e.getTime(), message: ee.toString(r) });
  }
  get minDate() {
    let e = null;
    for (const r of this._def.checks) r.kind === "min" && (e === null || r.value > e) && (e = r.value);
    return e != null ? new Date(e) : null;
  }
  get maxDate() {
    let e = null;
    for (const r of this._def.checks) r.kind === "max" && (e === null || r.value < e) && (e = r.value);
    return e != null ? new Date(e) : null;
  }
}
Ur.create = (s) => new Ur({ checks: [], coerce: (s == null ? void 0 : s.coerce) || !1, typeName: ue.ZodDate, ...ge(s) });
class xn extends xe {
  _parse(e) {
    if (this._getType(e) !== G.symbol) {
      const r = this._getOrReturnCtx(e);
      return Q(r, { code: M.invalid_type, expected: G.symbol, received: r.parsedType }), ie;
    }
    return gt(e.data);
  }
}
xn.create = (s) => new xn({ typeName: ue.ZodSymbol, ...ge(s) });
class vs extends xe {
  _parse(e) {
    if (this._getType(e) !== G.undefined) {
      const r = this._getOrReturnCtx(e);
      return Q(r, { code: M.invalid_type, expected: G.undefined, received: r.parsedType }), ie;
    }
    return gt(e.data);
  }
}
vs.create = (s) => new vs({ typeName: ue.ZodUndefined, ...ge(s) });
class gs extends xe {
  _parse(e) {
    if (this._getType(e) !== G.null) {
      const r = this._getOrReturnCtx(e);
      return Q(r, { code: M.invalid_type, expected: G.null, received: r.parsedType }), ie;
    }
    return gt(e.data);
  }
}
gs.create = (s) => new gs({ typeName: ue.ZodNull, ...ge(s) });
class En extends xe {
  constructor() {
    super(...arguments), this._any = !0;
  }
  _parse(e) {
    return gt(e.data);
  }
}
En.create = (s) => new En({ typeName: ue.ZodAny, ...ge(s) });
class ys extends xe {
  constructor() {
    super(...arguments), this._unknown = !0;
  }
  _parse(e) {
    return gt(e.data);
  }
}
ys.create = (s) => new ys({ typeName: ue.ZodUnknown, ...ge(s) });
class Gt extends xe {
  _parse(e) {
    const r = this._getOrReturnCtx(e);
    return Q(r, { code: M.invalid_type, expected: G.never, received: r.parsedType }), ie;
  }
}
Gt.create = (s) => new Gt({ typeName: ue.ZodNever, ...ge(s) });
class Rn extends xe {
  _parse(e) {
    if (this._getType(e) !== G.undefined) {
      const r = this._getOrReturnCtx(e);
      return Q(r, { code: M.invalid_type, expected: G.void, received: r.parsedType }), ie;
    }
    return gt(e.data);
  }
}
Rn.create = (s) => new Rn({ typeName: ue.ZodVoid, ...ge(s) });
class Ot extends xe {
  _parse(e) {
    const { ctx: r, status: a } = this._processInputParams(e), t = this._def;
    if (r.parsedType !== G.array) return Q(r, { code: M.invalid_type, expected: G.array, received: r.parsedType }), ie;
    if (t.exactLength !== null) {
      const i = r.data.length > t.exactLength.value, o = r.data.length < t.exactLength.value;
      (i || o) && (Q(r, { code: i ? M.too_big : M.too_small, minimum: o ? t.exactLength.value : void 0, maximum: i ? t.exactLength.value : void 0, type: "array", inclusive: !0, exact: !0, message: t.exactLength.message }), a.dirty());
    }
    if (t.minLength !== null && r.data.length < t.minLength.value && (Q(r, { code: M.too_small, minimum: t.minLength.value, type: "array", inclusive: !0, exact: !1, message: t.minLength.message }), a.dirty()), t.maxLength !== null && r.data.length > t.maxLength.value && (Q(r, { code: M.too_big, maximum: t.maxLength.value, type: "array", inclusive: !0, exact: !1, message: t.maxLength.message }), a.dirty()), r.common.async) return Promise.all([...r.data].map((i, o) => t.type._parseAsync(new $t(r, i, r.path, o)))).then((i) => it.mergeArray(a, i));
    const n = [...r.data].map((i, o) => t.type._parseSync(new $t(r, i, r.path, o)));
    return it.mergeArray(a, n);
  }
  get element() {
    return this._def.type;
  }
  min(e, r) {
    return new Ot({ ...this._def, minLength: { value: e, message: ee.toString(r) } });
  }
  max(e, r) {
    return new Ot({ ...this._def, maxLength: { value: e, message: ee.toString(r) } });
  }
  length(e, r) {
    return new Ot({ ...this._def, exactLength: { value: e, message: ee.toString(r) } });
  }
  nonempty(e) {
    return this.min(1, e);
  }
}
function dr(s) {
  if (s instanceof Be) {
    const e = {};
    for (const r in s.shape) {
      const a = s.shape[r];
      e[r] = zt.create(dr(a));
    }
    return new Be({ ...s._def, shape: () => e });
  }
  return s instanceof Ot ? new Ot({ ...s._def, type: dr(s.element) }) : s instanceof zt ? zt.create(dr(s.unwrap())) : s instanceof sr ? sr.create(dr(s.unwrap())) : s instanceof rr ? rr.create(s.items.map((e) => dr(e))) : s;
}
Ot.create = (s, e) => new Ot({ type: s, minLength: null, maxLength: null, exactLength: null, typeName: ue.ZodArray, ...ge(e) });
class Be extends xe {
  constructor() {
    super(...arguments), this._cached = null, this.nonstrict = this.passthrough, this.augment = this.extend;
  }
  _getCached() {
    if (this._cached !== null) return this._cached;
    const e = this._def.shape(), r = Re.objectKeys(e);
    return this._cached = { shape: e, keys: r }, this._cached;
  }
  _parse(e) {
    if (this._getType(e) !== G.object) {
      const u = this._getOrReturnCtx(e);
      return Q(u, { code: M.invalid_type, expected: G.object, received: u.parsedType }), ie;
    }
    const { status: r, ctx: a } = this._processInputParams(e), { shape: t, keys: n } = this._getCached(), i = [];
    if (!(this._def.catchall instanceof Gt && this._def.unknownKeys === "strip")) for (const u in a.data) n.includes(u) || i.push(u);
    const o = [];
    for (const u of n) {
      const l = t[u], h = a.data[u];
      o.push({ key: { status: "valid", value: u }, value: l._parse(new $t(a, h, a.path, u)), alwaysSet: u in a.data });
    }
    if (this._def.catchall instanceof Gt) {
      const u = this._def.unknownKeys;
      if (u === "passthrough") for (const l of i) o.push({ key: { status: "valid", value: l }, value: { status: "valid", value: a.data[l] } });
      else if (u === "strict") i.length > 0 && (Q(a, { code: M.unrecognized_keys, keys: i }), r.dirty());
      else if (u !== "strip") throw new Error("Internal ZodObject error: invalid unknownKeys value.");
    } else {
      const u = this._def.catchall;
      for (const l of i) {
        const h = a.data[l];
        o.push({ key: { status: "valid", value: l }, value: u._parse(new $t(a, h, a.path, l)), alwaysSet: l in a.data });
      }
    }
    return a.common.async ? Promise.resolve().then(async () => {
      const u = [];
      for (const l of o) {
        const h = await l.key, m = await l.value;
        u.push({ key: h, value: m, alwaysSet: l.alwaysSet });
      }
      return u;
    }).then((u) => it.mergeObjectSync(r, u)) : it.mergeObjectSync(r, o);
  }
  get shape() {
    return this._def.shape();
  }
  strict(e) {
    return ee.errToObj, new Be({ ...this._def, unknownKeys: "strict", ...e !== void 0 ? { errorMap: (r, a) => {
      var n, i;
      const t = ((i = (n = this._def).errorMap) == null ? void 0 : i.call(n, r, a).message) ?? a.defaultError;
      return r.code === "unrecognized_keys" ? { message: ee.errToObj(e).message ?? t } : { message: t };
    } } : {} });
  }
  strip() {
    return new Be({ ...this._def, unknownKeys: "strip" });
  }
  passthrough() {
    return new Be({ ...this._def, unknownKeys: "passthrough" });
  }
  extend(e) {
    return new Be({ ...this._def, shape: () => ({ ...this._def.shape(), ...e }) });
  }
  merge(e) {
    return new Be({ unknownKeys: e._def.unknownKeys, catchall: e._def.catchall, shape: () => ({ ...this._def.shape(), ...e._def.shape() }), typeName: ue.ZodObject });
  }
  setKey(e, r) {
    return this.augment({ [e]: r });
  }
  catchall(e) {
    return new Be({ ...this._def, catchall: e });
  }
  pick(e) {
    const r = {};
    for (const a of Re.objectKeys(e)) e[a] && this.shape[a] && (r[a] = this.shape[a]);
    return new Be({ ...this._def, shape: () => r });
  }
  omit(e) {
    const r = {};
    for (const a of Re.objectKeys(this.shape)) e[a] || (r[a] = this.shape[a]);
    return new Be({ ...this._def, shape: () => r });
  }
  deepPartial() {
    return dr(this);
  }
  partial(e) {
    const r = {};
    for (const a of Re.objectKeys(this.shape)) {
      const t = this.shape[a];
      e && !e[a] ? r[a] = t : r[a] = t.optional();
    }
    return new Be({ ...this._def, shape: () => r });
  }
  required(e) {
    const r = {};
    for (const a of Re.objectKeys(this.shape)) if (e && !e[a]) r[a] = this.shape[a];
    else {
      let t = this.shape[a];
      for (; t instanceof zt; ) t = t._def.innerType;
      r[a] = t;
    }
    return new Be({ ...this._def, shape: () => r });
  }
  keyof() {
    return Hi(Re.objectKeys(this.shape));
  }
}
Be.create = (s, e) => new Be({ shape: () => s, unknownKeys: "strip", catchall: Gt.create(), typeName: ue.ZodObject, ...ge(e) }), Be.strictCreate = (s, e) => new Be({ shape: () => s, unknownKeys: "strict", catchall: Gt.create(), typeName: ue.ZodObject, ...ge(e) }), Be.lazycreate = (s, e) => new Be({ shape: s, unknownKeys: "strip", catchall: Gt.create(), typeName: ue.ZodObject, ...ge(e) });
class Vr extends xe {
  _parse(e) {
    const { ctx: r } = this._processInputParams(e), a = this._def.options;
    if (r.common.async) return Promise.all(a.map(async (t) => {
      const n = { ...r, common: { ...r.common, issues: [] }, parent: null };
      return { result: await t._parseAsync({ data: r.data, path: r.path, parent: n }), ctx: n };
    })).then(function(t) {
      for (const i of t) if (i.result.status === "valid") return i.result;
      for (const i of t) if (i.result.status === "dirty") return r.common.issues.push(...i.ctx.common.issues), i.result;
      const n = t.map((i) => new Vt(i.ctx.common.issues));
      return Q(r, { code: M.invalid_union, unionErrors: n }), ie;
    });
    {
      let t;
      const n = [];
      for (const o of a) {
        const u = { ...r, common: { ...r.common, issues: [] }, parent: null }, l = o._parseSync({ data: r.data, path: r.path, parent: u });
        if (l.status === "valid") return l;
        l.status !== "dirty" || t || (t = { result: l, ctx: u }), u.common.issues.length && n.push(u.common.issues);
      }
      if (t) return r.common.issues.push(...t.ctx.common.issues), t.result;
      const i = n.map((o) => new Vt(o));
      return Q(r, { code: M.invalid_union, unionErrors: i }), ie;
    }
  }
  get options() {
    return this._def.options;
  }
}
Vr.create = (s, e) => new Vr({ options: s, typeName: ue.ZodUnion, ...ge(e) });
const Kt = (s) => s instanceof bs ? Kt(s.schema) : s instanceof tr ? Kt(s.innerType()) : s instanceof Br ? [s.value] : s instanceof ar ? s.options : s instanceof Ps ? Re.objectValues(s.enum) : s instanceof Kr ? Kt(s._def.innerType) : s instanceof vs ? [void 0] : s instanceof gs ? [null] : s instanceof zt ? [void 0, ...Kt(s.unwrap())] : s instanceof sr ? [null, ...Kt(s.unwrap())] : s instanceof Bi || s instanceof Wr ? Kt(s.unwrap()) : s instanceof Jr ? Kt(s._def.innerType) : [];
class Ms extends xe {
  _parse(e) {
    const { ctx: r } = this._processInputParams(e);
    if (r.parsedType !== G.object) return Q(r, { code: M.invalid_type, expected: G.object, received: r.parsedType }), ie;
    const a = this.discriminator, t = r.data[a], n = this.optionsMap.get(t);
    return n ? r.common.async ? n._parseAsync({ data: r.data, path: r.path, parent: r }) : n._parseSync({ data: r.data, path: r.path, parent: r }) : (Q(r, { code: M.invalid_union_discriminator, options: Array.from(this.optionsMap.keys()), path: [a] }), ie);
  }
  get discriminator() {
    return this._def.discriminator;
  }
  get options() {
    return this._def.options;
  }
  get optionsMap() {
    return this._def.optionsMap;
  }
  static create(e, r, a) {
    const t = /* @__PURE__ */ new Map();
    for (const n of r) {
      const i = Kt(n.shape[e]);
      if (!i.length) throw new Error(`A discriminator value for key \`${e}\` could not be extracted from all schema options`);
      for (const o of i) {
        if (t.has(o)) throw new Error(`Discriminator property ${String(e)} has duplicate value ${String(o)}`);
        t.set(o, n);
      }
    }
    return new Ms({ typeName: ue.ZodDiscriminatedUnion, discriminator: e, options: r, optionsMap: t, ...ge(a) });
  }
}
function _s(s, e) {
  const r = Jt(s), a = Jt(e);
  if (s === e) return { valid: !0, data: s };
  if (r === G.object && a === G.object) {
    const t = Re.objectKeys(e), n = Re.objectKeys(s).filter((o) => t.indexOf(o) !== -1), i = { ...s, ...e };
    for (const o of n) {
      const u = _s(s[o], e[o]);
      if (!u.valid) return { valid: !1 };
      i[o] = u.data;
    }
    return { valid: !0, data: i };
  }
  if (r === G.array && a === G.array) {
    if (s.length !== e.length) return { valid: !1 };
    const t = [];
    for (let n = 0; n < s.length; n++) {
      const i = _s(s[n], e[n]);
      if (!i.valid) return { valid: !1 };
      t.push(i.data);
    }
    return { valid: !0, data: t };
  }
  return r === G.date && a === G.date && +s == +e ? { valid: !0, data: s } : { valid: !1 };
}
class Hr extends xe {
  _parse(e) {
    const { status: r, ctx: a } = this._processInputParams(e), t = (n, i) => {
      if (Pn(n) || Pn(i)) return ie;
      const o = _s(n.value, i.value);
      return o.valid ? ((Sn(n) || Sn(i)) && r.dirty(), { status: r.value, value: o.data }) : (Q(a, { code: M.invalid_intersection_types }), ie);
    };
    return a.common.async ? Promise.all([this._def.left._parseAsync({ data: a.data, path: a.path, parent: a }), this._def.right._parseAsync({ data: a.data, path: a.path, parent: a })]).then(([n, i]) => t(n, i)) : t(this._def.left._parseSync({ data: a.data, path: a.path, parent: a }), this._def.right._parseSync({ data: a.data, path: a.path, parent: a }));
  }
}
Hr.create = (s, e, r) => new Hr({ left: s, right: e, typeName: ue.ZodIntersection, ...ge(r) });
class rr extends xe {
  _parse(e) {
    const { status: r, ctx: a } = this._processInputParams(e);
    if (a.parsedType !== G.array) return Q(a, { code: M.invalid_type, expected: G.array, received: a.parsedType }), ie;
    if (a.data.length < this._def.items.length) return Q(a, { code: M.too_small, minimum: this._def.items.length, inclusive: !0, exact: !1, type: "array" }), ie;
    !this._def.rest && a.data.length > this._def.items.length && (Q(a, { code: M.too_big, maximum: this._def.items.length, inclusive: !0, exact: !1, type: "array" }), r.dirty());
    const t = [...a.data].map((n, i) => {
      const o = this._def.items[i] || this._def.rest;
      return o ? o._parse(new $t(a, n, a.path, i)) : null;
    }).filter((n) => !!n);
    return a.common.async ? Promise.all(t).then((n) => it.mergeArray(r, n)) : it.mergeArray(r, t);
  }
  get items() {
    return this._def.items;
  }
  rest(e) {
    return new rr({ ...this._def, rest: e });
  }
}
rr.create = (s, e) => {
  if (!Array.isArray(s)) throw new Error("You must pass an array of schemas to z.tuple([ ... ])");
  return new rr({ items: s, typeName: ue.ZodTuple, rest: null, ...ge(e) });
};
class zs extends xe {
  get keySchema() {
    return this._def.keyType;
  }
  get valueSchema() {
    return this._def.valueType;
  }
  _parse(e) {
    const { status: r, ctx: a } = this._processInputParams(e);
    if (a.parsedType !== G.object) return Q(a, { code: M.invalid_type, expected: G.object, received: a.parsedType }), ie;
    const t = [], n = this._def.keyType, i = this._def.valueType;
    for (const o in a.data) t.push({ key: n._parse(new $t(a, o, a.path, o)), value: i._parse(new $t(a, a.data[o], a.path, o)), alwaysSet: o in a.data });
    return a.common.async ? it.mergeObjectAsync(r, t) : it.mergeObjectSync(r, t);
  }
  get element() {
    return this._def.valueType;
  }
  static create(e, r, a) {
    return new zs(r instanceof xe ? { keyType: e, valueType: r, typeName: ue.ZodRecord, ...ge(a) } : { keyType: Zt.create(), valueType: e, typeName: ue.ZodRecord, ...ge(r) });
  }
}
class kn extends xe {
  get keySchema() {
    return this._def.keyType;
  }
  get valueSchema() {
    return this._def.valueType;
  }
  _parse(e) {
    const { status: r, ctx: a } = this._processInputParams(e);
    if (a.parsedType !== G.map) return Q(a, { code: M.invalid_type, expected: G.map, received: a.parsedType }), ie;
    const t = this._def.keyType, n = this._def.valueType, i = [...a.data.entries()].map(([o, u], l) => ({ key: t._parse(new $t(a, o, a.path, [l, "key"])), value: n._parse(new $t(a, u, a.path, [l, "value"])) }));
    if (a.common.async) {
      const o = /* @__PURE__ */ new Map();
      return Promise.resolve().then(async () => {
        for (const u of i) {
          const l = await u.key, h = await u.value;
          if (l.status === "aborted" || h.status === "aborted") return ie;
          l.status !== "dirty" && h.status !== "dirty" || r.dirty(), o.set(l.value, h.value);
        }
        return { status: r.value, value: o };
      });
    }
    {
      const o = /* @__PURE__ */ new Map();
      for (const u of i) {
        const l = u.key, h = u.value;
        if (l.status === "aborted" || h.status === "aborted") return ie;
        l.status !== "dirty" && h.status !== "dirty" || r.dirty(), o.set(l.value, h.value);
      }
      return { status: r.value, value: o };
    }
  }
}
kn.create = (s, e, r) => new kn({ valueType: e, keyType: s, typeName: ue.ZodMap, ...ge(r) });
class kr extends xe {
  _parse(e) {
    const { status: r, ctx: a } = this._processInputParams(e);
    if (a.parsedType !== G.set) return Q(a, { code: M.invalid_type, expected: G.set, received: a.parsedType }), ie;
    const t = this._def;
    t.minSize !== null && a.data.size < t.minSize.value && (Q(a, { code: M.too_small, minimum: t.minSize.value, type: "set", inclusive: !0, exact: !1, message: t.minSize.message }), r.dirty()), t.maxSize !== null && a.data.size > t.maxSize.value && (Q(a, { code: M.too_big, maximum: t.maxSize.value, type: "set", inclusive: !0, exact: !1, message: t.maxSize.message }), r.dirty());
    const n = this._def.valueType;
    function i(u) {
      const l = /* @__PURE__ */ new Set();
      for (const h of u) {
        if (h.status === "aborted") return ie;
        h.status === "dirty" && r.dirty(), l.add(h.value);
      }
      return { status: r.value, value: l };
    }
    const o = [...a.data.values()].map((u, l) => n._parse(new $t(a, u, a.path, l)));
    return a.common.async ? Promise.all(o).then((u) => i(u)) : i(o);
  }
  min(e, r) {
    return new kr({ ...this._def, minSize: { value: e, message: ee.toString(r) } });
  }
  max(e, r) {
    return new kr({ ...this._def, maxSize: { value: e, message: ee.toString(r) } });
  }
  size(e, r) {
    return this.min(e, r).max(e, r);
  }
  nonempty(e) {
    return this.min(1, e);
  }
}
kr.create = (s, e) => new kr({ valueType: s, minSize: null, maxSize: null, typeName: ue.ZodSet, ...ge(e) });
class bs extends xe {
  get schema() {
    return this._def.getter();
  }
  _parse(e) {
    const { ctx: r } = this._processInputParams(e);
    return this._def.getter()._parse({ data: r.data, path: r.path, parent: r });
  }
}
bs.create = (s, e) => new bs({ getter: s, typeName: ue.ZodLazy, ...ge(e) });
class Br extends xe {
  _parse(e) {
    if (e.data !== this._def.value) {
      const r = this._getOrReturnCtx(e);
      return Q(r, { received: r.data, code: M.invalid_literal, expected: this._def.value }), ie;
    }
    return { status: "valid", value: e.data };
  }
  get value() {
    return this._def.value;
  }
}
function Hi(s, e) {
  return new ar({ values: s, typeName: ue.ZodEnum, ...ge(e) });
}
Br.create = (s, e) => new Br({ value: s, typeName: ue.ZodLiteral, ...ge(e) });
class ar extends xe {
  _parse(e) {
    if (typeof e.data != "string") {
      const r = this._getOrReturnCtx(e), a = this._def.values;
      return Q(r, { expected: Re.joinValues(a), received: r.parsedType, code: M.invalid_type }), ie;
    }
    if (this._cache || (this._cache = new Set(this._def.values)), !this._cache.has(e.data)) {
      const r = this._getOrReturnCtx(e), a = this._def.values;
      return Q(r, { received: r.data, code: M.invalid_enum_value, options: a }), ie;
    }
    return gt(e.data);
  }
  get options() {
    return this._def.values;
  }
  get enum() {
    const e = {};
    for (const r of this._def.values) e[r] = r;
    return e;
  }
  get Values() {
    const e = {};
    for (const r of this._def.values) e[r] = r;
    return e;
  }
  get Enum() {
    const e = {};
    for (const r of this._def.values) e[r] = r;
    return e;
  }
  extract(e, r = this._def) {
    return ar.create(e, { ...this._def, ...r });
  }
  exclude(e, r = this._def) {
    return ar.create(this.options.filter((a) => !e.includes(a)), { ...this._def, ...r });
  }
}
ar.create = Hi;
class Ps extends xe {
  _parse(e) {
    const r = Re.getValidEnumValues(this._def.values), a = this._getOrReturnCtx(e);
    if (a.parsedType !== G.string && a.parsedType !== G.number) {
      const t = Re.objectValues(r);
      return Q(a, { expected: Re.joinValues(t), received: a.parsedType, code: M.invalid_type }), ie;
    }
    if (this._cache || (this._cache = new Set(Re.getValidEnumValues(this._def.values))), !this._cache.has(e.data)) {
      const t = Re.objectValues(r);
      return Q(a, { received: a.data, code: M.invalid_enum_value, options: t }), ie;
    }
    return gt(e.data);
  }
  get enum() {
    return this._def.values;
  }
}
Ps.create = (s, e) => new Ps({ values: s, typeName: ue.ZodNativeEnum, ...ge(e) });
class Qr extends xe {
  unwrap() {
    return this._def.type;
  }
  _parse(e) {
    const { ctx: r } = this._processInputParams(e);
    if (r.parsedType !== G.promise && r.common.async === !1) return Q(r, { code: M.invalid_type, expected: G.promise, received: r.parsedType }), ie;
    const a = r.parsedType === G.promise ? r.data : Promise.resolve(r.data);
    return gt(a.then((t) => this._def.type.parseAsync(t, { path: r.path, errorMap: r.common.contextualErrorMap })));
  }
}
Qr.create = (s, e) => new Qr({ type: s, typeName: ue.ZodPromise, ...ge(e) });
class tr extends xe {
  innerType() {
    return this._def.schema;
  }
  sourceType() {
    return this._def.schema._def.typeName === ue.ZodEffects ? this._def.schema.sourceType() : this._def.schema;
  }
  _parse(e) {
    const { status: r, ctx: a } = this._processInputParams(e), t = this._def.effect || null, n = { addIssue: (i) => {
      Q(a, i), i.fatal ? r.abort() : r.dirty();
    }, get path() {
      return a.path;
    } };
    if (n.addIssue = n.addIssue.bind(n), t.type === "preprocess") {
      const i = t.transform(a.data, n);
      if (a.common.async) return Promise.resolve(i).then(async (o) => {
        if (r.value === "aborted") return ie;
        const u = await this._def.schema._parseAsync({ data: o, path: a.path, parent: a });
        return u.status === "aborted" ? ie : u.status === "dirty" || r.value === "dirty" ? ps(u.value) : u;
      });
      {
        if (r.value === "aborted") return ie;
        const o = this._def.schema._parseSync({ data: i, path: a.path, parent: a });
        return o.status === "aborted" ? ie : o.status === "dirty" || r.value === "dirty" ? ps(o.value) : o;
      }
    }
    if (t.type === "refinement") {
      const i = (o) => {
        const u = t.refinement(o, n);
        if (a.common.async) return Promise.resolve(u);
        if (u instanceof Promise) throw new Error("Async refinement encountered during synchronous parse operation. Use .parseAsync instead.");
        return o;
      };
      if (a.common.async === !1) {
        const o = this._def.schema._parseSync({ data: a.data, path: a.path, parent: a });
        return o.status === "aborted" ? ie : (o.status === "dirty" && r.dirty(), i(o.value), { status: r.value, value: o.value });
      }
      return this._def.schema._parseAsync({ data: a.data, path: a.path, parent: a }).then((o) => o.status === "aborted" ? ie : (o.status === "dirty" && r.dirty(), i(o.value).then(() => ({ status: r.value, value: o.value }))));
    }
    if (t.type === "transform") {
      if (a.common.async === !1) {
        const i = this._def.schema._parseSync({ data: a.data, path: a.path, parent: a });
        if (!fr(i)) return ie;
        const o = t.transform(i.value, n);
        if (o instanceof Promise) throw new Error("Asynchronous transform encountered during synchronous parse operation. Use .parseAsync instead.");
        return { status: r.value, value: o };
      }
      return this._def.schema._parseAsync({ data: a.data, path: a.path, parent: a }).then((i) => fr(i) ? Promise.resolve(t.transform(i.value, n)).then((o) => ({ status: r.value, value: o })) : ie);
    }
    Re.assertNever(t);
  }
}
tr.create = (s, e, r) => new tr({ schema: s, typeName: ue.ZodEffects, effect: e, ...ge(r) }), tr.createWithPreprocess = (s, e, r) => new tr({ schema: e, effect: { type: "preprocess", transform: s }, typeName: ue.ZodEffects, ...ge(r) });
class zt extends xe {
  _parse(e) {
    return this._getType(e) === G.undefined ? gt(void 0) : this._def.innerType._parse(e);
  }
  unwrap() {
    return this._def.innerType;
  }
}
zt.create = (s, e) => new zt({ innerType: s, typeName: ue.ZodOptional, ...ge(e) });
class sr extends xe {
  _parse(e) {
    return this._getType(e) === G.null ? gt(null) : this._def.innerType._parse(e);
  }
  unwrap() {
    return this._def.innerType;
  }
}
sr.create = (s, e) => new sr({ innerType: s, typeName: ue.ZodNullable, ...ge(e) });
class Kr extends xe {
  _parse(e) {
    const { ctx: r } = this._processInputParams(e);
    let a = r.data;
    return r.parsedType === G.undefined && (a = this._def.defaultValue()), this._def.innerType._parse({ data: a, path: r.path, parent: r });
  }
  removeDefault() {
    return this._def.innerType;
  }
}
Kr.create = (s, e) => new Kr({ innerType: s, typeName: ue.ZodDefault, defaultValue: typeof e.default == "function" ? e.default : () => e.default, ...ge(e) });
class Jr extends xe {
  _parse(e) {
    const { ctx: r } = this._processInputParams(e), a = { ...r, common: { ...r.common, issues: [] } }, t = this._def.innerType._parse({ data: a.data, path: a.path, parent: { ...a } });
    return zr(t) ? t.then((n) => ({ status: "valid", value: n.status === "valid" ? n.value : this._def.catchValue({ get error() {
      return new Vt(a.common.issues);
    }, input: a.data }) })) : { status: "valid", value: t.status === "valid" ? t.value : this._def.catchValue({ get error() {
      return new Vt(a.common.issues);
    }, input: a.data }) };
  }
  removeCatch() {
    return this._def.innerType;
  }
}
Jr.create = (s, e) => new Jr({ innerType: s, typeName: ue.ZodCatch, catchValue: typeof e.catch == "function" ? e.catch : () => e.catch, ...ge(e) });
class Tn extends xe {
  _parse(e) {
    if (this._getType(e) !== G.nan) {
      const r = this._getOrReturnCtx(e);
      return Q(r, { code: M.invalid_type, expected: G.nan, received: r.parsedType }), ie;
    }
    return { status: "valid", value: e.data };
  }
}
Tn.create = (s) => new Tn({ typeName: ue.ZodNaN, ...ge(s) });
class Bi extends xe {
  _parse(e) {
    const { ctx: r } = this._processInputParams(e), a = r.data;
    return this._def.type._parse({ data: a, path: r.path, parent: r });
  }
  unwrap() {
    return this._def.type;
  }
}
class Us extends xe {
  _parse(e) {
    const { status: r, ctx: a } = this._processInputParams(e);
    if (a.common.async) return (async () => {
      const t = await this._def.in._parseAsync({ data: a.data, path: a.path, parent: a });
      return t.status === "aborted" ? ie : t.status === "dirty" ? (r.dirty(), ps(t.value)) : this._def.out._parseAsync({ data: t.value, path: a.path, parent: a });
    })();
    {
      const t = this._def.in._parseSync({ data: a.data, path: a.path, parent: a });
      return t.status === "aborted" ? ie : t.status === "dirty" ? (r.dirty(), { status: "dirty", value: t.value }) : this._def.out._parseSync({ data: t.value, path: a.path, parent: a });
    }
  }
  static create(e, r) {
    return new Us({ in: e, out: r, typeName: ue.ZodPipeline });
  }
}
class Wr extends xe {
  _parse(e) {
    const r = this._def.innerType._parse(e), a = (t) => (fr(t) && (t.value = Object.freeze(t.value)), t);
    return zr(r) ? r.then((t) => a(t)) : a(r);
  }
  unwrap() {
    return this._def.innerType;
  }
}
var ue;
Wr.create = (s, e) => new Wr({ innerType: s, typeName: ue.ZodReadonly, ...ge(e) }), function(s) {
  s.ZodString = "ZodString", s.ZodNumber = "ZodNumber", s.ZodNaN = "ZodNaN", s.ZodBigInt = "ZodBigInt", s.ZodBoolean = "ZodBoolean", s.ZodDate = "ZodDate", s.ZodSymbol = "ZodSymbol", s.ZodUndefined = "ZodUndefined", s.ZodNull = "ZodNull", s.ZodAny = "ZodAny", s.ZodUnknown = "ZodUnknown", s.ZodNever = "ZodNever", s.ZodVoid = "ZodVoid", s.ZodArray = "ZodArray", s.ZodObject = "ZodObject", s.ZodUnion = "ZodUnion", s.ZodDiscriminatedUnion = "ZodDiscriminatedUnion", s.ZodIntersection = "ZodIntersection", s.ZodTuple = "ZodTuple", s.ZodRecord = "ZodRecord", s.ZodMap = "ZodMap", s.ZodSet = "ZodSet", s.ZodFunction = "ZodFunction", s.ZodLazy = "ZodLazy", s.ZodLiteral = "ZodLiteral", s.ZodEnum = "ZodEnum", s.ZodEffects = "ZodEffects", s.ZodNativeEnum = "ZodNativeEnum", s.ZodOptional = "ZodOptional", s.ZodNullable = "ZodNullable", s.ZodDefault = "ZodDefault", s.ZodCatch = "ZodCatch", s.ZodPromise = "ZodPromise", s.ZodBranded = "ZodBranded", s.ZodPipeline = "ZodPipeline", s.ZodReadonly = "ZodReadonly";
}(ue || (ue = {}));
const J = Zt.create, nt = pr.create, pt = ms.create, Tr = ys.create, We = (Gt.create, Ot.create), X = Be.create, st = Vr.create, wu = Ms.create, Cr = (Hr.create, rr.create, zs.create), pe = Br.create, Ht = ar.create, F = (Qr.create, zt.create), ua = (sr.create, "2.0"), Qi = st([J(), nt().int()]), Ki = J(), xu = X({ progressToken: F(Qi) }).passthrough(), yt = X({ _meta: F(xu) }).passthrough(), ht = X({ method: J(), params: F(yt) }), $r = X({ _meta: F(X({}).passthrough()) }).passthrough(), Dt = X({ method: J(), params: F($r) }), _t = X({ _meta: F(X({}).passthrough()) }).passthrough(), la = st([J(), nt().int()]), Eu = X({ jsonrpc: pe(ua), id: la }).merge(ht).strict(), Ru = X({ jsonrpc: pe(ua) }).merge(Dt).strict(), ku = X({ jsonrpc: pe(ua), id: la, result: _t }).strict();
var An;
(function(s) {
  s[s.ConnectionClosed = -32e3] = "ConnectionClosed", s[s.RequestTimeout = -32001] = "RequestTimeout", s[s.ParseError = -32700] = "ParseError", s[s.InvalidRequest = -32600] = "InvalidRequest", s[s.MethodNotFound = -32601] = "MethodNotFound", s[s.InvalidParams = -32602] = "InvalidParams", s[s.InternalError = -32603] = "InternalError";
})(An || (An = {}));
const Tu = st([Eu, Ru, ku, X({ jsonrpc: pe(ua), id: la, error: X({ code: nt().int(), message: J(), data: F(Tr()) }) }).strict()]), On = _t.strict(), Cn = Dt.extend({ method: pe("notifications/cancelled"), params: $r.extend({ requestId: la, reason: J().optional() }) }), Ir = X({ name: J(), title: F(J()) }).passthrough(), Ji = Ir.extend({ version: J() }), Au = X({ experimental: F(X({}).passthrough()), sampling: F(X({}).passthrough()), elicitation: F(X({}).passthrough()), roots: F(X({ listChanged: F(pt()) }).passthrough()) }).passthrough(), Ou = ht.extend({ method: pe("initialize"), params: yt.extend({ protocolVersion: J(), capabilities: Au, clientInfo: Ji }) }), Cu = X({ experimental: F(X({}).passthrough()), logging: F(X({}).passthrough()), completions: F(X({}).passthrough()), prompts: F(X({ listChanged: F(pt()) }).passthrough()), resources: F(X({ subscribe: F(pt()), listChanged: F(pt()) }).passthrough()), tools: F(X({ listChanged: F(pt()) }).passthrough()) }).passthrough(), $u = _t.extend({ protocolVersion: J(), capabilities: Cu, serverInfo: Ji, instructions: F(J()) }), Iu = Dt.extend({ method: pe("notifications/initialized") }), $n = ht.extend({ method: pe("ping") }), Nu = X({ progress: nt(), total: F(nt()), message: F(J()) }).passthrough(), In = Dt.extend({ method: pe("notifications/progress"), params: $r.merge(Nu).extend({ progressToken: Qi }) }), ca = ht.extend({ params: yt.extend({ cursor: F(Ki) }).optional() }), da = _t.extend({ nextCursor: F(Ki) }), Wi = X({ uri: J(), mimeType: F(J()), _meta: F(X({}).passthrough()) }).passthrough(), Gi = Wi.extend({ text: J() }), Yi = Wi.extend({ blob: J().base64() }), Xi = Ir.extend({ uri: J(), description: F(J()), mimeType: F(J()), _meta: F(X({}).passthrough()) }), Du = Ir.extend({ uriTemplate: J(), description: F(J()), mimeType: F(J()), _meta: F(X({}).passthrough()) }), ju = ca.extend({ method: pe("resources/list") }), Fu = da.extend({ resources: We(Xi) }), qu = ca.extend({ method: pe("resources/templates/list") }), Lu = da.extend({ resourceTemplates: We(Du) }), Zu = ht.extend({ method: pe("resources/read"), params: yt.extend({ uri: J() }) }), Mu = _t.extend({ contents: We(st([Gi, Yi])) }), zu = Dt.extend({ method: pe("notifications/resources/list_changed") }), Uu = ht.extend({ method: pe("resources/subscribe"), params: yt.extend({ uri: J() }) }), Vu = ht.extend({ method: pe("resources/unsubscribe"), params: yt.extend({ uri: J() }) }), Hu = Dt.extend({ method: pe("notifications/resources/updated"), params: $r.extend({ uri: J() }) }), Bu = X({ name: J(), description: F(J()), required: F(pt()) }).passthrough(), Qu = Ir.extend({ description: F(J()), arguments: F(We(Bu)), _meta: F(X({}).passthrough()) }), Ku = ca.extend({ method: pe("prompts/list") }), Ju = da.extend({ prompts: We(Qu) }), Wu = ht.extend({ method: pe("prompts/get"), params: yt.extend({ name: J(), arguments: F(Cr(J())) }) }), Vs = X({ type: pe("text"), text: J(), _meta: F(X({}).passthrough()) }).passthrough(), Hs = X({ type: pe("image"), data: J().base64(), mimeType: J(), _meta: F(X({}).passthrough()) }).passthrough(), Bs = X({ type: pe("audio"), data: J().base64(), mimeType: J(), _meta: F(X({}).passthrough()) }).passthrough(), Gu = X({ type: pe("resource"), resource: st([Gi, Yi]), _meta: F(X({}).passthrough()) }).passthrough(), eo = st([Vs, Hs, Bs, Xi.extend({ type: pe("resource_link") }), Gu]), Yu = X({ role: Ht(["user", "assistant"]), content: eo }).passthrough(), Xu = _t.extend({ description: F(J()), messages: We(Yu) }), el = Dt.extend({ method: pe("notifications/prompts/list_changed") }), tl = X({ title: F(J()), readOnlyHint: F(pt()), destructiveHint: F(pt()), idempotentHint: F(pt()), openWorldHint: F(pt()) }).passthrough(), rl = Ir.extend({ description: F(J()), inputSchema: X({ type: pe("object"), properties: F(X({}).passthrough()), required: F(We(J())) }).passthrough(), outputSchema: F(X({ type: pe("object"), properties: F(X({}).passthrough()), required: F(We(J())) }).passthrough()), annotations: F(tl), _meta: F(X({}).passthrough()) }), al = ca.extend({ method: pe("tools/list") }), sl = da.extend({ tools: We(rl) }), to = _t.extend({ content: We(eo).default([]), structuredContent: X({}).passthrough().optional(), isError: F(pt()) }), nl = (to.or(_t.extend({ toolResult: Tr() })), ht.extend({ method: pe("tools/call"), params: yt.extend({ name: J(), arguments: F(Cr(Tr())) }) })), il = Dt.extend({ method: pe("notifications/tools/list_changed") }), ro = Ht(["debug", "info", "notice", "warning", "error", "critical", "alert", "emergency"]), ol = ht.extend({ method: pe("logging/setLevel"), params: yt.extend({ level: ro }) }), ul = Dt.extend({ method: pe("notifications/message"), params: $r.extend({ level: ro, logger: F(J()), data: Tr() }) }), ll = X({ name: J().optional() }).passthrough(), cl = X({ hints: F(We(ll)), costPriority: F(nt().min(0).max(1)), speedPriority: F(nt().min(0).max(1)), intelligencePriority: F(nt().min(0).max(1)) }).passthrough(), dl = X({ role: Ht(["user", "assistant"]), content: st([Vs, Hs, Bs]) }).passthrough(), hl = ht.extend({ method: pe("sampling/createMessage"), params: yt.extend({ messages: We(dl), systemPrompt: F(J()), includeContext: F(Ht(["none", "thisServer", "allServers"])), temperature: F(nt()), maxTokens: nt().int(), stopSequences: F(We(J())), metadata: F(X({}).passthrough()), modelPreferences: F(cl) }) }), fl = _t.extend({ model: J(), stopReason: F(Ht(["endTurn", "stopSequence", "maxTokens"]).or(J())), role: Ht(["user", "assistant"]), content: wu("type", [Vs, Hs, Bs]) }), pl = st([X({ type: pe("boolean"), title: F(J()), description: F(J()), default: F(pt()) }).passthrough(), X({ type: pe("string"), title: F(J()), description: F(J()), minLength: F(nt()), maxLength: F(nt()), format: F(Ht(["email", "uri", "date", "date-time"])) }).passthrough(), X({ type: Ht(["number", "integer"]), title: F(J()), description: F(J()), minimum: F(nt()), maximum: F(nt()) }).passthrough(), X({ type: pe("string"), title: F(J()), description: F(J()), enum: We(J()), enumNames: F(We(J())) }).passthrough()]), ml = ht.extend({ method: pe("elicitation/create"), params: yt.extend({ message: J(), requestedSchema: X({ type: pe("object"), properties: Cr(J(), pl), required: F(We(J())) }).passthrough() }) }), vl = _t.extend({ action: Ht(["accept", "reject", "cancel"]), content: F(Cr(J(), Tr())) }), gl = X({ type: pe("ref/resource"), uri: J() }).passthrough(), yl = X({ type: pe("ref/prompt"), name: J() }).passthrough(), _l = ht.extend({ method: pe("completion/complete"), params: yt.extend({ ref: st([yl, gl]), argument: X({ name: J(), value: J() }).passthrough(), context: F(X({ arguments: F(Cr(J(), J())) })) }) }), bl = _t.extend({ completion: X({ values: We(J()).max(100), total: F(nt().int()), hasMore: F(pt()) }).passthrough() }), Pl = X({ uri: J().startsWith("file://"), name: F(J()), _meta: F(X({}).passthrough()) }).passthrough(), Sl = ht.extend({ method: pe("roots/list") }), wl = _t.extend({ roots: We(Pl) }), xl = Dt.extend({ method: pe("notifications/roots/list_changed") });
st([$n, Ou, _l, ol, Wu, Ku, ju, qu, Zu, Uu, Vu, nl, al]), st([Cn, In, Iu, xl]), st([On, fl, vl, wl]), st([$n, hl, ml, Sl]), st([Cn, In, ul, Hu, zu, il, el]), st([On, $u, bl, Xu, Ju, Fu, Lu, Mu, to, sl]);
class Qs {
  constructor(e, r) {
    Xe(this, "sessionId");
    Xe(this, "onmessage");
    Xe(this, "onerror");
    Xe(this, "onclose");
    Xe(this, "_port");
    Xe(this, "_started", !1);
    Xe(this, "_closed", !1);
    if (!e) throw new Error("MessagePort is required");
    this._port = e, this.sessionId = r || this.generateId(), this._port.onmessage = (a) => {
      var t, n;
      try {
        const i = Tu.parse(a.data);
        (t = this.onmessage) == null || t.call(this, i);
      } catch (i) {
        const o = new Error(`Failed to parse message: ${i}`);
        (n = this.onerror) == null || n.call(this, o);
      }
    }, this._port.onmessageerror = (a) => {
      var n;
      const t = new Error(`MessagePort error: ${JSON.stringify(a)}`);
      (n = this.onerror) == null || n.call(this, t);
    };
  }
  static generateSessionId() {
    return typeof crypto < "u" && typeof crypto.randomUUID == "function" ? crypto.randomUUID() : `${Date.now().toString(36)}-${Math.random().toString(36).substring(2, 10)}`;
  }
  async start() {
    if (this._started) throw new Error("BrowserContextTransport already started! If using Client or Server class, note that connect() calls start() automatically.");
    if (this._closed) throw new Error("Cannot start a closed BrowserContextTransport");
    this._started = !0, this._port.start();
  }
  async send(e) {
    if (this._closed) throw new Error("Cannot send on a closed BrowserContextTransport");
    return new Promise((r, a) => {
      var t;
      try {
        this._port.postMessage(e), r();
      } catch (n) {
        const i = n instanceof Error ? n : new Error(String(n));
        (t = this.onerror) == null || t.call(this, i), a(i);
      }
    });
  }
  async close() {
    var e;
    this._closed || (this._closed = !0, this._port.close(), (e = this.onclose) == null || e.call(this));
  }
  generateId() {
    return Qs.generateSessionId();
  }
}
class El {
  constructor() {
    Xe(this, "angieDetector");
    Xe(this, "registrationQueue");
    Xe(this, "clientManager");
    Xe(this, "isInitialized", !1);
    this.angieDetector = new eu(), this.registrationQueue = new tu(), this.clientManager = new ru(), this.setupAngieReadyHandler(), this.setupServerInitHandler();
  }
  setupAngieReadyHandler() {
    this.angieDetector.waitForReady().then((e) => {
      e.isReady ? this.handleAngieReady() : console.warn("AngieMcpSdk: Angie not detected - servers will remain queued");
    }).catch((e) => {
      console.error("AngieMcpSdk: Error waiting for Angie:", e);
    });
  }
  async handleAngieReady() {
    console.log("AngieMcpSdk: Angie is ready, processing queued registrations");
    try {
      await this.registrationQueue.processQueue(async (e) => {
        await this.processRegistration(e);
      }), this.isInitialized = !0, console.log("AngieMcpSdk: Initialization complete");
    } catch (e) {
      console.error("AngieMcpSdk: Error processing registration queue:", e);
    }
  }
  async processRegistration(e) {
    console.log(`AngieMcpSdk: Processing registration for server "${e.config.name}"`);
    try {
      await this.clientManager.requestClientCreation(e), console.log(`AngieMcpSdk: Successfully registered server "${e.config.name}"`);
    } catch (r) {
      throw console.error(`AngieMcpSdk: Failed to register server "${e.config.name}":`, r), r;
    }
  }
  async registerServer(e) {
    if (!e.server) throw new Error("Server instance is required");
    if (!e.name) throw new Error("Server name is required");
    if (!e.description) throw new Error("Server description is required");
    console.log(`AngieMcpSdk: Registering server "${e.name}"`);
    const r = this.registrationQueue.add(e);
    if (this.angieDetector.isReady()) try {
      await this.processRegistration(r), this.registrationQueue.updateStatus(r.id, "registered"), console.log(`AngieMcpSdk: Server "${e.name}" registered successfully`);
    } catch (a) {
      const t = a instanceof Error ? a.message : String(a);
      throw this.registrationQueue.updateStatus(r.id, "failed", t), a;
    }
    else console.log(`AngieMcpSdk: Server "${e.name}" queued until Angie is ready`);
  }
  getRegistrations() {
    return this.registrationQueue.getAll();
  }
  getPendingRegistrations() {
    return this.registrationQueue.getPending();
  }
  isAngieReady() {
    return this.angieDetector.isReady();
  }
  isReady() {
    return this.isInitialized;
  }
  async waitForReady() {
    if (!(await this.angieDetector.waitForReady()).isReady) throw new Error("Angie is not available");
    for (; !this.isInitialized; ) await new Promise((e) => setTimeout(e, 100));
  }
  destroy() {
    this.registrationQueue.clear(), console.log("AngieMcpSdk: SDK destroyed");
  }
  setupServerInitHandler() {
    window.addEventListener("message", (e) => {
      var r;
      ((r = e.data) == null ? void 0 : r.type) === Er.SDK_REQUEST_INIT_SERVER && this.handleServerInitRequest(e);
    });
  }
  handleServerInitRequest(e) {
    const { clientId: r, serverId: a } = e.data.payload || {};
    if (r && a) {
      console.log(`AngieMcpSdk: Handling server init request for clientId: ${r}, serverId: ${a}`);
      try {
        const t = this.registrationQueue.getAll().find((u) => u.id === a);
        if (!t) return void console.error(`AngieMcpSdk: No registration found for serverId: ${a}`);
        const n = e.ports[0];
        if (!n) return void console.error("AngieMcpSdk: No port provided in server init request");
        const i = t.config.server, o = new Qs(n);
        i.connect(o), console.log(`AngieMcpSdk: Server "${t.config.name}" initialized successfully`);
      } catch (t) {
        console.error(`AngieMcpSdk: Error initializing server for clientId ${r}:`, t);
      }
    } else console.error("AngieMcpSdk: Invalid server init request - missing clientId or serverId");
  }
}
var ke;
(function(s) {
  s.assertEqual = (t) => {
  };
  function e(t) {
  }
  s.assertIs = e;
  function r(t) {
    throw new Error();
  }
  s.assertNever = r, s.arrayToEnum = (t) => {
    const n = {};
    for (const i of t)
      n[i] = i;
    return n;
  }, s.getValidEnumValues = (t) => {
    const n = s.objectKeys(t).filter((o) => typeof t[t[o]] != "number"), i = {};
    for (const o of n)
      i[o] = t[o];
    return s.objectValues(i);
  }, s.objectValues = (t) => s.objectKeys(t).map(function(n) {
    return t[n];
  }), s.objectKeys = typeof Object.keys == "function" ? (t) => Object.keys(t) : (t) => {
    const n = [];
    for (const i in t)
      Object.prototype.hasOwnProperty.call(t, i) && n.push(i);
    return n;
  }, s.find = (t, n) => {
    for (const i of t)
      if (n(i))
        return i;
  }, s.isInteger = typeof Number.isInteger == "function" ? (t) => Number.isInteger(t) : (t) => typeof t == "number" && Number.isFinite(t) && Math.floor(t) === t;
  function a(t, n = " | ") {
    return t.map((i) => typeof i == "string" ? `'${i}'` : i).join(n);
  }
  s.joinValues = a, s.jsonStringifyReplacer = (t, n) => typeof n == "bigint" ? n.toString() : n;
})(ke || (ke = {}));
var Nn;
(function(s) {
  s.mergeShapes = (e, r) => ({
    ...e,
    ...r
    // second overwrites first
  });
})(Nn || (Nn = {}));
const Y = ke.arrayToEnum([
  "string",
  "nan",
  "number",
  "integer",
  "float",
  "boolean",
  "date",
  "bigint",
  "symbol",
  "function",
  "undefined",
  "null",
  "array",
  "object",
  "unknown",
  "promise",
  "void",
  "never",
  "map",
  "set"
]), Wt = (s) => {
  switch (typeof s) {
    case "undefined":
      return Y.undefined;
    case "string":
      return Y.string;
    case "number":
      return Number.isNaN(s) ? Y.nan : Y.number;
    case "boolean":
      return Y.boolean;
    case "function":
      return Y.function;
    case "bigint":
      return Y.bigint;
    case "symbol":
      return Y.symbol;
    case "object":
      return Array.isArray(s) ? Y.array : s === null ? Y.null : s.then && typeof s.then == "function" && s.catch && typeof s.catch == "function" ? Y.promise : typeof Map < "u" && s instanceof Map ? Y.map : typeof Set < "u" && s instanceof Set ? Y.set : typeof Date < "u" && s instanceof Date ? Y.date : Y.object;
    default:
      return Y.unknown;
  }
}, z = ke.arrayToEnum([
  "invalid_type",
  "invalid_literal",
  "custom",
  "invalid_union",
  "invalid_union_discriminator",
  "invalid_enum_value",
  "unrecognized_keys",
  "invalid_arguments",
  "invalid_return_type",
  "invalid_date",
  "invalid_string",
  "too_small",
  "too_big",
  "invalid_intersection_types",
  "not_multiple_of",
  "not_finite"
]);
class Bt extends Error {
  get errors() {
    return this.issues;
  }
  constructor(e) {
    super(), this.issues = [], this.addIssue = (a) => {
      this.issues = [...this.issues, a];
    }, this.addIssues = (a = []) => {
      this.issues = [...this.issues, ...a];
    };
    const r = new.target.prototype;
    Object.setPrototypeOf ? Object.setPrototypeOf(this, r) : this.__proto__ = r, this.name = "ZodError", this.issues = e;
  }
  format(e) {
    const r = e || function(n) {
      return n.message;
    }, a = { _errors: [] }, t = (n) => {
      for (const i of n.issues)
        if (i.code === "invalid_union")
          i.unionErrors.map(t);
        else if (i.code === "invalid_return_type")
          t(i.returnTypeError);
        else if (i.code === "invalid_arguments")
          t(i.argumentsError);
        else if (i.path.length === 0)
          a._errors.push(r(i));
        else {
          let o = a, u = 0;
          for (; u < i.path.length; ) {
            const l = i.path[u];
            u === i.path.length - 1 ? (o[l] = o[l] || { _errors: [] }, o[l]._errors.push(r(i))) : o[l] = o[l] || { _errors: [] }, o = o[l], u++;
          }
        }
    };
    return t(this), a;
  }
  static assert(e) {
    if (!(e instanceof Bt))
      throw new Error(`Not a ZodError: ${e}`);
  }
  toString() {
    return this.message;
  }
  get message() {
    return JSON.stringify(this.issues, ke.jsonStringifyReplacer, 2);
  }
  get isEmpty() {
    return this.issues.length === 0;
  }
  flatten(e = (r) => r.message) {
    const r = {}, a = [];
    for (const t of this.issues)
      if (t.path.length > 0) {
        const n = t.path[0];
        r[n] = r[n] || [], r[n].push(e(t));
      } else
        a.push(e(t));
    return { formErrors: a, fieldErrors: r };
  }
  get formErrors() {
    return this.flatten();
  }
}
Bt.create = (s) => new Bt(s);
const Ss = (s, e) => {
  let r;
  switch (s.code) {
    case z.invalid_type:
      s.received === Y.undefined ? r = "Required" : r = `Expected ${s.expected}, received ${s.received}`;
      break;
    case z.invalid_literal:
      r = `Invalid literal value, expected ${JSON.stringify(s.expected, ke.jsonStringifyReplacer)}`;
      break;
    case z.unrecognized_keys:
      r = `Unrecognized key(s) in object: ${ke.joinValues(s.keys, ", ")}`;
      break;
    case z.invalid_union:
      r = "Invalid input";
      break;
    case z.invalid_union_discriminator:
      r = `Invalid discriminator value. Expected ${ke.joinValues(s.options)}`;
      break;
    case z.invalid_enum_value:
      r = `Invalid enum value. Expected ${ke.joinValues(s.options)}, received '${s.received}'`;
      break;
    case z.invalid_arguments:
      r = "Invalid function arguments";
      break;
    case z.invalid_return_type:
      r = "Invalid function return type";
      break;
    case z.invalid_date:
      r = "Invalid date";
      break;
    case z.invalid_string:
      typeof s.validation == "object" ? "includes" in s.validation ? (r = `Invalid input: must include "${s.validation.includes}"`, typeof s.validation.position == "number" && (r = `${r} at one or more positions greater than or equal to ${s.validation.position}`)) : "startsWith" in s.validation ? r = `Invalid input: must start with "${s.validation.startsWith}"` : "endsWith" in s.validation ? r = `Invalid input: must end with "${s.validation.endsWith}"` : ke.assertNever(s.validation) : s.validation !== "regex" ? r = `Invalid ${s.validation}` : r = "Invalid";
      break;
    case z.too_small:
      s.type === "array" ? r = `Array must contain ${s.exact ? "exactly" : s.inclusive ? "at least" : "more than"} ${s.minimum} element(s)` : s.type === "string" ? r = `String must contain ${s.exact ? "exactly" : s.inclusive ? "at least" : "over"} ${s.minimum} character(s)` : s.type === "number" ? r = `Number must be ${s.exact ? "exactly equal to " : s.inclusive ? "greater than or equal to " : "greater than "}${s.minimum}` : s.type === "bigint" ? r = `Number must be ${s.exact ? "exactly equal to " : s.inclusive ? "greater than or equal to " : "greater than "}${s.minimum}` : s.type === "date" ? r = `Date must be ${s.exact ? "exactly equal to " : s.inclusive ? "greater than or equal to " : "greater than "}${new Date(Number(s.minimum))}` : r = "Invalid input";
      break;
    case z.too_big:
      s.type === "array" ? r = `Array must contain ${s.exact ? "exactly" : s.inclusive ? "at most" : "less than"} ${s.maximum} element(s)` : s.type === "string" ? r = `String must contain ${s.exact ? "exactly" : s.inclusive ? "at most" : "under"} ${s.maximum} character(s)` : s.type === "number" ? r = `Number must be ${s.exact ? "exactly" : s.inclusive ? "less than or equal to" : "less than"} ${s.maximum}` : s.type === "bigint" ? r = `BigInt must be ${s.exact ? "exactly" : s.inclusive ? "less than or equal to" : "less than"} ${s.maximum}` : s.type === "date" ? r = `Date must be ${s.exact ? "exactly" : s.inclusive ? "smaller than or equal to" : "smaller than"} ${new Date(Number(s.maximum))}` : r = "Invalid input";
      break;
    case z.custom:
      r = "Invalid input";
      break;
    case z.invalid_intersection_types:
      r = "Intersection results could not be merged";
      break;
    case z.not_multiple_of:
      r = `Number must be a multiple of ${s.multipleOf}`;
      break;
    case z.not_finite:
      r = "Number must be finite";
      break;
    default:
      r = e.defaultError, ke.assertNever(s);
  }
  return { message: r };
};
let Rl = Ss;
function kl() {
  return Rl;
}
const Tl = (s) => {
  const { data: e, path: r, errorMaps: a, issueData: t } = s, n = [...r, ...t.path || []], i = {
    ...t,
    path: n
  };
  if (t.message !== void 0)
    return {
      ...t,
      path: n,
      message: t.message
    };
  let o = "";
  const u = a.filter((l) => !!l).slice().reverse();
  for (const l of u)
    o = l(i, { data: e, defaultError: o }).message;
  return {
    ...t,
    path: n,
    message: o
  };
};
function K(s, e) {
  const r = kl(), a = Tl({
    issueData: e,
    data: s.data,
    path: s.path,
    errorMaps: [
      s.common.contextualErrorMap,
      // contextual error map is first priority
      s.schemaErrorMap,
      // then schema-bound map if available
      r,
      // then global override map
      r === Ss ? void 0 : Ss
      // then global default map
    ].filter((t) => !!t)
  });
  s.common.issues.push(a);
}
class ot {
  constructor() {
    this.value = "valid";
  }
  dirty() {
    this.value === "valid" && (this.value = "dirty");
  }
  abort() {
    this.value !== "aborted" && (this.value = "aborted");
  }
  static mergeArray(e, r) {
    const a = [];
    for (const t of r) {
      if (t.status === "aborted")
        return oe;
      t.status === "dirty" && e.dirty(), a.push(t.value);
    }
    return { status: e.value, value: a };
  }
  static async mergeObjectAsync(e, r) {
    const a = [];
    for (const t of r) {
      const n = await t.key, i = await t.value;
      a.push({
        key: n,
        value: i
      });
    }
    return ot.mergeObjectSync(e, a);
  }
  static mergeObjectSync(e, r) {
    const a = {};
    for (const t of r) {
      const { key: n, value: i } = t;
      if (n.status === "aborted" || i.status === "aborted")
        return oe;
      n.status === "dirty" && e.dirty(), i.status === "dirty" && e.dirty(), n.value !== "__proto__" && (typeof i.value < "u" || t.alwaysSet) && (a[n.value] = i.value);
    }
    return { status: e.value, value: a };
  }
}
const oe = Object.freeze({
  status: "aborted"
}), wr = (s) => ({ status: "dirty", value: s }), bt = (s) => ({ status: "valid", value: s }), Dn = (s) => s.status === "aborted", jn = (s) => s.status === "dirty", mr = (s) => s.status === "valid", Gr = (s) => typeof Promise < "u" && s instanceof Promise;
var te;
(function(s) {
  s.errToObj = (e) => typeof e == "string" ? { message: e } : e || {}, s.toString = (e) => typeof e == "string" ? e : e == null ? void 0 : e.message;
})(te || (te = {}));
class It {
  constructor(e, r, a, t) {
    this._cachedPath = [], this.parent = e, this.data = r, this._path = a, this._key = t;
  }
  get path() {
    return this._cachedPath.length || (Array.isArray(this._key) ? this._cachedPath.push(...this._path, ...this._key) : this._cachedPath.push(...this._path, this._key)), this._cachedPath;
  }
}
const Fn = (s, e) => {
  if (mr(e))
    return { success: !0, data: e.value };
  if (!s.common.issues.length)
    throw new Error("Validation failed but no issues detected.");
  return {
    success: !1,
    get error() {
      if (this._error)
        return this._error;
      const r = new Bt(s.common.issues);
      return this._error = r, this._error;
    }
  };
};
function ye(s) {
  if (!s)
    return {};
  const { errorMap: e, invalid_type_error: r, required_error: a, description: t } = s;
  if (e && (r || a))
    throw new Error(`Can't use "invalid_type_error" or "required_error" in conjunction with custom error map.`);
  return e ? { errorMap: e, description: t } : { errorMap: (i, o) => {
    const { message: u } = s;
    return i.code === "invalid_enum_value" ? { message: u ?? o.defaultError } : typeof o.data > "u" ? { message: u ?? a ?? o.defaultError } : i.code !== "invalid_type" ? { message: o.defaultError } : { message: u ?? r ?? o.defaultError };
  }, description: t };
}
class Pe {
  get description() {
    return this._def.description;
  }
  _getType(e) {
    return Wt(e.data);
  }
  _getOrReturnCtx(e, r) {
    return r || {
      common: e.parent.common,
      data: e.data,
      parsedType: Wt(e.data),
      schemaErrorMap: this._def.errorMap,
      path: e.path,
      parent: e.parent
    };
  }
  _processInputParams(e) {
    return {
      status: new ot(),
      ctx: {
        common: e.parent.common,
        data: e.data,
        parsedType: Wt(e.data),
        schemaErrorMap: this._def.errorMap,
        path: e.path,
        parent: e.parent
      }
    };
  }
  _parseSync(e) {
    const r = this._parse(e);
    if (Gr(r))
      throw new Error("Synchronous parse encountered promise.");
    return r;
  }
  _parseAsync(e) {
    const r = this._parse(e);
    return Promise.resolve(r);
  }
  parse(e, r) {
    const a = this.safeParse(e, r);
    if (a.success)
      return a.data;
    throw a.error;
  }
  safeParse(e, r) {
    const a = {
      common: {
        issues: [],
        async: (r == null ? void 0 : r.async) ?? !1,
        contextualErrorMap: r == null ? void 0 : r.errorMap
      },
      path: (r == null ? void 0 : r.path) || [],
      schemaErrorMap: this._def.errorMap,
      parent: null,
      data: e,
      parsedType: Wt(e)
    }, t = this._parseSync({ data: e, path: a.path, parent: a });
    return Fn(a, t);
  }
  "~validate"(e) {
    var a, t;
    const r = {
      common: {
        issues: [],
        async: !!this["~standard"].async
      },
      path: [],
      schemaErrorMap: this._def.errorMap,
      parent: null,
      data: e,
      parsedType: Wt(e)
    };
    if (!this["~standard"].async)
      try {
        const n = this._parseSync({ data: e, path: [], parent: r });
        return mr(n) ? {
          value: n.value
        } : {
          issues: r.common.issues
        };
      } catch (n) {
        (t = (a = n == null ? void 0 : n.message) == null ? void 0 : a.toLowerCase()) != null && t.includes("encountered") && (this["~standard"].async = !0), r.common = {
          issues: [],
          async: !0
        };
      }
    return this._parseAsync({ data: e, path: [], parent: r }).then((n) => mr(n) ? {
      value: n.value
    } : {
      issues: r.common.issues
    });
  }
  async parseAsync(e, r) {
    const a = await this.safeParseAsync(e, r);
    if (a.success)
      return a.data;
    throw a.error;
  }
  async safeParseAsync(e, r) {
    const a = {
      common: {
        issues: [],
        contextualErrorMap: r == null ? void 0 : r.errorMap,
        async: !0
      },
      path: (r == null ? void 0 : r.path) || [],
      schemaErrorMap: this._def.errorMap,
      parent: null,
      data: e,
      parsedType: Wt(e)
    }, t = this._parse({ data: e, path: a.path, parent: a }), n = await (Gr(t) ? t : Promise.resolve(t));
    return Fn(a, n);
  }
  refine(e, r) {
    const a = (t) => typeof r == "string" || typeof r > "u" ? { message: r } : typeof r == "function" ? r(t) : r;
    return this._refinement((t, n) => {
      const i = e(t), o = () => n.addIssue({
        code: z.custom,
        ...a(t)
      });
      return typeof Promise < "u" && i instanceof Promise ? i.then((u) => u ? !0 : (o(), !1)) : i ? !0 : (o(), !1);
    });
  }
  refinement(e, r) {
    return this._refinement((a, t) => e(a) ? !0 : (t.addIssue(typeof r == "function" ? r(a, t) : r), !1));
  }
  _refinement(e) {
    return new or({
      schema: this,
      typeName: U.ZodEffects,
      effect: { type: "refinement", refinement: e }
    });
  }
  superRefine(e) {
    return this._refinement(e);
  }
  constructor(e) {
    this.spa = this.safeParseAsync, this._def = e, this.parse = this.parse.bind(this), this.safeParse = this.safeParse.bind(this), this.parseAsync = this.parseAsync.bind(this), this.safeParseAsync = this.safeParseAsync.bind(this), this.spa = this.spa.bind(this), this.refine = this.refine.bind(this), this.refinement = this.refinement.bind(this), this.superRefine = this.superRefine.bind(this), this.optional = this.optional.bind(this), this.nullable = this.nullable.bind(this), this.nullish = this.nullish.bind(this), this.array = this.array.bind(this), this.promise = this.promise.bind(this), this.or = this.or.bind(this), this.and = this.and.bind(this), this.transform = this.transform.bind(this), this.brand = this.brand.bind(this), this.default = this.default.bind(this), this.catch = this.catch.bind(this), this.describe = this.describe.bind(this), this.pipe = this.pipe.bind(this), this.readonly = this.readonly.bind(this), this.isNullable = this.isNullable.bind(this), this.isOptional = this.isOptional.bind(this), this["~standard"] = {
      version: 1,
      vendor: "zod",
      validate: (r) => this["~validate"](r)
    };
  }
  optional() {
    return Ut.create(this, this._def);
  }
  nullable() {
    return ur.create(this, this._def);
  }
  nullish() {
    return this.nullable().optional();
  }
  array() {
    return Ct.create(this);
  }
  promise() {
    return aa.create(this, this._def);
  }
  or(e) {
    return Xr.create([this, e], this._def);
  }
  and(e) {
    return ea.create(this, e, this._def);
  }
  transform(e) {
    return new or({
      ...ye(this._def),
      schema: this,
      typeName: U.ZodEffects,
      effect: { type: "transform", transform: e }
    });
  }
  default(e) {
    const r = typeof e == "function" ? e : () => e;
    return new sa({
      ...ye(this._def),
      innerType: this,
      defaultValue: r,
      typeName: U.ZodDefault
    });
  }
  brand() {
    return new io({
      typeName: U.ZodBranded,
      type: this,
      ...ye(this._def)
    });
  }
  catch(e) {
    const r = typeof e == "function" ? e : () => e;
    return new na({
      ...ye(this._def),
      innerType: this,
      catchValue: r,
      typeName: U.ZodCatch
    });
  }
  describe(e) {
    const r = this.constructor;
    return new r({
      ...this._def,
      description: e
    });
  }
  pipe(e) {
    return Js.create(this, e);
  }
  readonly() {
    return ia.create(this);
  }
  isOptional() {
    return this.safeParse(void 0).success;
  }
  isNullable() {
    return this.safeParse(null).success;
  }
}
const Al = /^c[^\s-]{8,}$/i, Ol = /^[0-9a-z]+$/, Cl = /^[0-9A-HJKMNP-TV-Z]{26}$/i, $l = /^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$/i, Il = /^[a-z0-9_-]{21}$/i, Nl = /^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]*$/, Dl = /^[-+]?P(?!$)(?:(?:[-+]?\d+Y)|(?:[-+]?\d+[.,]\d+Y$))?(?:(?:[-+]?\d+M)|(?:[-+]?\d+[.,]\d+M$))?(?:(?:[-+]?\d+W)|(?:[-+]?\d+[.,]\d+W$))?(?:(?:[-+]?\d+D)|(?:[-+]?\d+[.,]\d+D$))?(?:T(?=[\d+-])(?:(?:[-+]?\d+H)|(?:[-+]?\d+[.,]\d+H$))?(?:(?:[-+]?\d+M)|(?:[-+]?\d+[.,]\d+M$))?(?:[-+]?\d+(?:[.,]\d+)?S)?)??$/, jl = /^(?!\.)(?!.*\.\.)([A-Z0-9_'+\-\.]*)[A-Z0-9_+-]@([A-Z0-9][A-Z0-9\-]*\.)+[A-Z]{2,}$/i, Fl = "^(\\p{Extended_Pictographic}|\\p{Emoji_Component})+$";
let ba;
const ql = /^(?:(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])\.){3}(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])$/, Ll = /^(?:(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])\.){3}(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])\/(3[0-2]|[12]?[0-9])$/, Zl = /^(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))$/, Ml = /^(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))\/(12[0-8]|1[01][0-9]|[1-9]?[0-9])$/, zl = /^([0-9a-zA-Z+/]{4})*(([0-9a-zA-Z+/]{2}==)|([0-9a-zA-Z+/]{3}=))?$/, Ul = /^([0-9a-zA-Z-_]{4})*(([0-9a-zA-Z-_]{2}(==)?)|([0-9a-zA-Z-_]{3}(=)?))?$/, ao = "((\\d\\d[2468][048]|\\d\\d[13579][26]|\\d\\d0[48]|[02468][048]00|[13579][26]00)-02-29|\\d{4}-((0[13578]|1[02])-(0[1-9]|[12]\\d|3[01])|(0[469]|11)-(0[1-9]|[12]\\d|30)|(02)-(0[1-9]|1\\d|2[0-8])))", Vl = new RegExp(`^${ao}$`);
function so(s) {
  let e = "[0-5]\\d";
  s.precision ? e = `${e}\\.\\d{${s.precision}}` : s.precision == null && (e = `${e}(\\.\\d+)?`);
  const r = s.precision ? "+" : "?";
  return `([01]\\d|2[0-3]):[0-5]\\d(:${e})${r}`;
}
function Hl(s) {
  return new RegExp(`^${so(s)}$`);
}
function Bl(s) {
  let e = `${ao}T${so(s)}`;
  const r = [];
  return r.push(s.local ? "Z?" : "Z"), s.offset && r.push("([+-]\\d{2}:?\\d{2})"), e = `${e}(${r.join("|")})`, new RegExp(`^${e}$`);
}
function Ql(s, e) {
  return !!((e === "v4" || !e) && ql.test(s) || (e === "v6" || !e) && Zl.test(s));
}
function Kl(s, e) {
  if (!Nl.test(s))
    return !1;
  try {
    const [r] = s.split(".");
    if (!r)
      return !1;
    const a = r.replace(/-/g, "+").replace(/_/g, "/").padEnd(r.length + (4 - r.length % 4) % 4, "="), t = JSON.parse(atob(a));
    return !(typeof t != "object" || t === null || "typ" in t && (t == null ? void 0 : t.typ) !== "JWT" || !t.alg || e && t.alg !== e);
  } catch {
    return !1;
  }
}
function Jl(s, e) {
  return !!((e === "v4" || !e) && Ll.test(s) || (e === "v6" || !e) && Ml.test(s));
}
class Mt extends Pe {
  _parse(e) {
    if (this._def.coerce && (e.data = String(e.data)), this._getType(e) !== Y.string) {
      const n = this._getOrReturnCtx(e);
      return K(n, {
        code: z.invalid_type,
        expected: Y.string,
        received: n.parsedType
      }), oe;
    }
    const a = new ot();
    let t;
    for (const n of this._def.checks)
      if (n.kind === "min")
        e.data.length < n.value && (t = this._getOrReturnCtx(e, t), K(t, {
          code: z.too_small,
          minimum: n.value,
          type: "string",
          inclusive: !0,
          exact: !1,
          message: n.message
        }), a.dirty());
      else if (n.kind === "max")
        e.data.length > n.value && (t = this._getOrReturnCtx(e, t), K(t, {
          code: z.too_big,
          maximum: n.value,
          type: "string",
          inclusive: !0,
          exact: !1,
          message: n.message
        }), a.dirty());
      else if (n.kind === "length") {
        const i = e.data.length > n.value, o = e.data.length < n.value;
        (i || o) && (t = this._getOrReturnCtx(e, t), i ? K(t, {
          code: z.too_big,
          maximum: n.value,
          type: "string",
          inclusive: !0,
          exact: !0,
          message: n.message
        }) : o && K(t, {
          code: z.too_small,
          minimum: n.value,
          type: "string",
          inclusive: !0,
          exact: !0,
          message: n.message
        }), a.dirty());
      } else if (n.kind === "email")
        jl.test(e.data) || (t = this._getOrReturnCtx(e, t), K(t, {
          validation: "email",
          code: z.invalid_string,
          message: n.message
        }), a.dirty());
      else if (n.kind === "emoji")
        ba || (ba = new RegExp(Fl, "u")), ba.test(e.data) || (t = this._getOrReturnCtx(e, t), K(t, {
          validation: "emoji",
          code: z.invalid_string,
          message: n.message
        }), a.dirty());
      else if (n.kind === "uuid")
        $l.test(e.data) || (t = this._getOrReturnCtx(e, t), K(t, {
          validation: "uuid",
          code: z.invalid_string,
          message: n.message
        }), a.dirty());
      else if (n.kind === "nanoid")
        Il.test(e.data) || (t = this._getOrReturnCtx(e, t), K(t, {
          validation: "nanoid",
          code: z.invalid_string,
          message: n.message
        }), a.dirty());
      else if (n.kind === "cuid")
        Al.test(e.data) || (t = this._getOrReturnCtx(e, t), K(t, {
          validation: "cuid",
          code: z.invalid_string,
          message: n.message
        }), a.dirty());
      else if (n.kind === "cuid2")
        Ol.test(e.data) || (t = this._getOrReturnCtx(e, t), K(t, {
          validation: "cuid2",
          code: z.invalid_string,
          message: n.message
        }), a.dirty());
      else if (n.kind === "ulid")
        Cl.test(e.data) || (t = this._getOrReturnCtx(e, t), K(t, {
          validation: "ulid",
          code: z.invalid_string,
          message: n.message
        }), a.dirty());
      else if (n.kind === "url")
        try {
          new URL(e.data);
        } catch {
          t = this._getOrReturnCtx(e, t), K(t, {
            validation: "url",
            code: z.invalid_string,
            message: n.message
          }), a.dirty();
        }
      else n.kind === "regex" ? (n.regex.lastIndex = 0, n.regex.test(e.data) || (t = this._getOrReturnCtx(e, t), K(t, {
        validation: "regex",
        code: z.invalid_string,
        message: n.message
      }), a.dirty())) : n.kind === "trim" ? e.data = e.data.trim() : n.kind === "includes" ? e.data.includes(n.value, n.position) || (t = this._getOrReturnCtx(e, t), K(t, {
        code: z.invalid_string,
        validation: { includes: n.value, position: n.position },
        message: n.message
      }), a.dirty()) : n.kind === "toLowerCase" ? e.data = e.data.toLowerCase() : n.kind === "toUpperCase" ? e.data = e.data.toUpperCase() : n.kind === "startsWith" ? e.data.startsWith(n.value) || (t = this._getOrReturnCtx(e, t), K(t, {
        code: z.invalid_string,
        validation: { startsWith: n.value },
        message: n.message
      }), a.dirty()) : n.kind === "endsWith" ? e.data.endsWith(n.value) || (t = this._getOrReturnCtx(e, t), K(t, {
        code: z.invalid_string,
        validation: { endsWith: n.value },
        message: n.message
      }), a.dirty()) : n.kind === "datetime" ? Bl(n).test(e.data) || (t = this._getOrReturnCtx(e, t), K(t, {
        code: z.invalid_string,
        validation: "datetime",
        message: n.message
      }), a.dirty()) : n.kind === "date" ? Vl.test(e.data) || (t = this._getOrReturnCtx(e, t), K(t, {
        code: z.invalid_string,
        validation: "date",
        message: n.message
      }), a.dirty()) : n.kind === "time" ? Hl(n).test(e.data) || (t = this._getOrReturnCtx(e, t), K(t, {
        code: z.invalid_string,
        validation: "time",
        message: n.message
      }), a.dirty()) : n.kind === "duration" ? Dl.test(e.data) || (t = this._getOrReturnCtx(e, t), K(t, {
        validation: "duration",
        code: z.invalid_string,
        message: n.message
      }), a.dirty()) : n.kind === "ip" ? Ql(e.data, n.version) || (t = this._getOrReturnCtx(e, t), K(t, {
        validation: "ip",
        code: z.invalid_string,
        message: n.message
      }), a.dirty()) : n.kind === "jwt" ? Kl(e.data, n.alg) || (t = this._getOrReturnCtx(e, t), K(t, {
        validation: "jwt",
        code: z.invalid_string,
        message: n.message
      }), a.dirty()) : n.kind === "cidr" ? Jl(e.data, n.version) || (t = this._getOrReturnCtx(e, t), K(t, {
        validation: "cidr",
        code: z.invalid_string,
        message: n.message
      }), a.dirty()) : n.kind === "base64" ? zl.test(e.data) || (t = this._getOrReturnCtx(e, t), K(t, {
        validation: "base64",
        code: z.invalid_string,
        message: n.message
      }), a.dirty()) : n.kind === "base64url" ? Ul.test(e.data) || (t = this._getOrReturnCtx(e, t), K(t, {
        validation: "base64url",
        code: z.invalid_string,
        message: n.message
      }), a.dirty()) : ke.assertNever(n);
    return { status: a.value, value: e.data };
  }
  _regex(e, r, a) {
    return this.refinement((t) => e.test(t), {
      validation: r,
      code: z.invalid_string,
      ...te.errToObj(a)
    });
  }
  _addCheck(e) {
    return new Mt({
      ...this._def,
      checks: [...this._def.checks, e]
    });
  }
  email(e) {
    return this._addCheck({ kind: "email", ...te.errToObj(e) });
  }
  url(e) {
    return this._addCheck({ kind: "url", ...te.errToObj(e) });
  }
  emoji(e) {
    return this._addCheck({ kind: "emoji", ...te.errToObj(e) });
  }
  uuid(e) {
    return this._addCheck({ kind: "uuid", ...te.errToObj(e) });
  }
  nanoid(e) {
    return this._addCheck({ kind: "nanoid", ...te.errToObj(e) });
  }
  cuid(e) {
    return this._addCheck({ kind: "cuid", ...te.errToObj(e) });
  }
  cuid2(e) {
    return this._addCheck({ kind: "cuid2", ...te.errToObj(e) });
  }
  ulid(e) {
    return this._addCheck({ kind: "ulid", ...te.errToObj(e) });
  }
  base64(e) {
    return this._addCheck({ kind: "base64", ...te.errToObj(e) });
  }
  base64url(e) {
    return this._addCheck({
      kind: "base64url",
      ...te.errToObj(e)
    });
  }
  jwt(e) {
    return this._addCheck({ kind: "jwt", ...te.errToObj(e) });
  }
  ip(e) {
    return this._addCheck({ kind: "ip", ...te.errToObj(e) });
  }
  cidr(e) {
    return this._addCheck({ kind: "cidr", ...te.errToObj(e) });
  }
  datetime(e) {
    return typeof e == "string" ? this._addCheck({
      kind: "datetime",
      precision: null,
      offset: !1,
      local: !1,
      message: e
    }) : this._addCheck({
      kind: "datetime",
      precision: typeof (e == null ? void 0 : e.precision) > "u" ? null : e == null ? void 0 : e.precision,
      offset: (e == null ? void 0 : e.offset) ?? !1,
      local: (e == null ? void 0 : e.local) ?? !1,
      ...te.errToObj(e == null ? void 0 : e.message)
    });
  }
  date(e) {
    return this._addCheck({ kind: "date", message: e });
  }
  time(e) {
    return typeof e == "string" ? this._addCheck({
      kind: "time",
      precision: null,
      message: e
    }) : this._addCheck({
      kind: "time",
      precision: typeof (e == null ? void 0 : e.precision) > "u" ? null : e == null ? void 0 : e.precision,
      ...te.errToObj(e == null ? void 0 : e.message)
    });
  }
  duration(e) {
    return this._addCheck({ kind: "duration", ...te.errToObj(e) });
  }
  regex(e, r) {
    return this._addCheck({
      kind: "regex",
      regex: e,
      ...te.errToObj(r)
    });
  }
  includes(e, r) {
    return this._addCheck({
      kind: "includes",
      value: e,
      position: r == null ? void 0 : r.position,
      ...te.errToObj(r == null ? void 0 : r.message)
    });
  }
  startsWith(e, r) {
    return this._addCheck({
      kind: "startsWith",
      value: e,
      ...te.errToObj(r)
    });
  }
  endsWith(e, r) {
    return this._addCheck({
      kind: "endsWith",
      value: e,
      ...te.errToObj(r)
    });
  }
  min(e, r) {
    return this._addCheck({
      kind: "min",
      value: e,
      ...te.errToObj(r)
    });
  }
  max(e, r) {
    return this._addCheck({
      kind: "max",
      value: e,
      ...te.errToObj(r)
    });
  }
  length(e, r) {
    return this._addCheck({
      kind: "length",
      value: e,
      ...te.errToObj(r)
    });
  }
  /**
   * Equivalent to `.min(1)`
   */
  nonempty(e) {
    return this.min(1, te.errToObj(e));
  }
  trim() {
    return new Mt({
      ...this._def,
      checks: [...this._def.checks, { kind: "trim" }]
    });
  }
  toLowerCase() {
    return new Mt({
      ...this._def,
      checks: [...this._def.checks, { kind: "toLowerCase" }]
    });
  }
  toUpperCase() {
    return new Mt({
      ...this._def,
      checks: [...this._def.checks, { kind: "toUpperCase" }]
    });
  }
  get isDatetime() {
    return !!this._def.checks.find((e) => e.kind === "datetime");
  }
  get isDate() {
    return !!this._def.checks.find((e) => e.kind === "date");
  }
  get isTime() {
    return !!this._def.checks.find((e) => e.kind === "time");
  }
  get isDuration() {
    return !!this._def.checks.find((e) => e.kind === "duration");
  }
  get isEmail() {
    return !!this._def.checks.find((e) => e.kind === "email");
  }
  get isURL() {
    return !!this._def.checks.find((e) => e.kind === "url");
  }
  get isEmoji() {
    return !!this._def.checks.find((e) => e.kind === "emoji");
  }
  get isUUID() {
    return !!this._def.checks.find((e) => e.kind === "uuid");
  }
  get isNANOID() {
    return !!this._def.checks.find((e) => e.kind === "nanoid");
  }
  get isCUID() {
    return !!this._def.checks.find((e) => e.kind === "cuid");
  }
  get isCUID2() {
    return !!this._def.checks.find((e) => e.kind === "cuid2");
  }
  get isULID() {
    return !!this._def.checks.find((e) => e.kind === "ulid");
  }
  get isIP() {
    return !!this._def.checks.find((e) => e.kind === "ip");
  }
  get isCIDR() {
    return !!this._def.checks.find((e) => e.kind === "cidr");
  }
  get isBase64() {
    return !!this._def.checks.find((e) => e.kind === "base64");
  }
  get isBase64url() {
    return !!this._def.checks.find((e) => e.kind === "base64url");
  }
  get minLength() {
    let e = null;
    for (const r of this._def.checks)
      r.kind === "min" && (e === null || r.value > e) && (e = r.value);
    return e;
  }
  get maxLength() {
    let e = null;
    for (const r of this._def.checks)
      r.kind === "max" && (e === null || r.value < e) && (e = r.value);
    return e;
  }
}
Mt.create = (s) => new Mt({
  checks: [],
  typeName: U.ZodString,
  coerce: (s == null ? void 0 : s.coerce) ?? !1,
  ...ye(s)
});
function Wl(s, e) {
  const r = (s.toString().split(".")[1] || "").length, a = (e.toString().split(".")[1] || "").length, t = r > a ? r : a, n = Number.parseInt(s.toFixed(t).replace(".", "")), i = Number.parseInt(e.toFixed(t).replace(".", ""));
  return n % i / 10 ** t;
}
class vr extends Pe {
  constructor() {
    super(...arguments), this.min = this.gte, this.max = this.lte, this.step = this.multipleOf;
  }
  _parse(e) {
    if (this._def.coerce && (e.data = Number(e.data)), this._getType(e) !== Y.number) {
      const n = this._getOrReturnCtx(e);
      return K(n, {
        code: z.invalid_type,
        expected: Y.number,
        received: n.parsedType
      }), oe;
    }
    let a;
    const t = new ot();
    for (const n of this._def.checks)
      n.kind === "int" ? ke.isInteger(e.data) || (a = this._getOrReturnCtx(e, a), K(a, {
        code: z.invalid_type,
        expected: "integer",
        received: "float",
        message: n.message
      }), t.dirty()) : n.kind === "min" ? (n.inclusive ? e.data < n.value : e.data <= n.value) && (a = this._getOrReturnCtx(e, a), K(a, {
        code: z.too_small,
        minimum: n.value,
        type: "number",
        inclusive: n.inclusive,
        exact: !1,
        message: n.message
      }), t.dirty()) : n.kind === "max" ? (n.inclusive ? e.data > n.value : e.data >= n.value) && (a = this._getOrReturnCtx(e, a), K(a, {
        code: z.too_big,
        maximum: n.value,
        type: "number",
        inclusive: n.inclusive,
        exact: !1,
        message: n.message
      }), t.dirty()) : n.kind === "multipleOf" ? Wl(e.data, n.value) !== 0 && (a = this._getOrReturnCtx(e, a), K(a, {
        code: z.not_multiple_of,
        multipleOf: n.value,
        message: n.message
      }), t.dirty()) : n.kind === "finite" ? Number.isFinite(e.data) || (a = this._getOrReturnCtx(e, a), K(a, {
        code: z.not_finite,
        message: n.message
      }), t.dirty()) : ke.assertNever(n);
    return { status: t.value, value: e.data };
  }
  gte(e, r) {
    return this.setLimit("min", e, !0, te.toString(r));
  }
  gt(e, r) {
    return this.setLimit("min", e, !1, te.toString(r));
  }
  lte(e, r) {
    return this.setLimit("max", e, !0, te.toString(r));
  }
  lt(e, r) {
    return this.setLimit("max", e, !1, te.toString(r));
  }
  setLimit(e, r, a, t) {
    return new vr({
      ...this._def,
      checks: [
        ...this._def.checks,
        {
          kind: e,
          value: r,
          inclusive: a,
          message: te.toString(t)
        }
      ]
    });
  }
  _addCheck(e) {
    return new vr({
      ...this._def,
      checks: [...this._def.checks, e]
    });
  }
  int(e) {
    return this._addCheck({
      kind: "int",
      message: te.toString(e)
    });
  }
  positive(e) {
    return this._addCheck({
      kind: "min",
      value: 0,
      inclusive: !1,
      message: te.toString(e)
    });
  }
  negative(e) {
    return this._addCheck({
      kind: "max",
      value: 0,
      inclusive: !1,
      message: te.toString(e)
    });
  }
  nonpositive(e) {
    return this._addCheck({
      kind: "max",
      value: 0,
      inclusive: !0,
      message: te.toString(e)
    });
  }
  nonnegative(e) {
    return this._addCheck({
      kind: "min",
      value: 0,
      inclusive: !0,
      message: te.toString(e)
    });
  }
  multipleOf(e, r) {
    return this._addCheck({
      kind: "multipleOf",
      value: e,
      message: te.toString(r)
    });
  }
  finite(e) {
    return this._addCheck({
      kind: "finite",
      message: te.toString(e)
    });
  }
  safe(e) {
    return this._addCheck({
      kind: "min",
      inclusive: !0,
      value: Number.MIN_SAFE_INTEGER,
      message: te.toString(e)
    })._addCheck({
      kind: "max",
      inclusive: !0,
      value: Number.MAX_SAFE_INTEGER,
      message: te.toString(e)
    });
  }
  get minValue() {
    let e = null;
    for (const r of this._def.checks)
      r.kind === "min" && (e === null || r.value > e) && (e = r.value);
    return e;
  }
  get maxValue() {
    let e = null;
    for (const r of this._def.checks)
      r.kind === "max" && (e === null || r.value < e) && (e = r.value);
    return e;
  }
  get isInt() {
    return !!this._def.checks.find((e) => e.kind === "int" || e.kind === "multipleOf" && ke.isInteger(e.value));
  }
  get isFinite() {
    let e = null, r = null;
    for (const a of this._def.checks) {
      if (a.kind === "finite" || a.kind === "int" || a.kind === "multipleOf")
        return !0;
      a.kind === "min" ? (r === null || a.value > r) && (r = a.value) : a.kind === "max" && (e === null || a.value < e) && (e = a.value);
    }
    return Number.isFinite(r) && Number.isFinite(e);
  }
}
vr.create = (s) => new vr({
  checks: [],
  typeName: U.ZodNumber,
  coerce: (s == null ? void 0 : s.coerce) || !1,
  ...ye(s)
});
class Ar extends Pe {
  constructor() {
    super(...arguments), this.min = this.gte, this.max = this.lte;
  }
  _parse(e) {
    if (this._def.coerce)
      try {
        e.data = BigInt(e.data);
      } catch {
        return this._getInvalidInput(e);
      }
    if (this._getType(e) !== Y.bigint)
      return this._getInvalidInput(e);
    let a;
    const t = new ot();
    for (const n of this._def.checks)
      n.kind === "min" ? (n.inclusive ? e.data < n.value : e.data <= n.value) && (a = this._getOrReturnCtx(e, a), K(a, {
        code: z.too_small,
        type: "bigint",
        minimum: n.value,
        inclusive: n.inclusive,
        message: n.message
      }), t.dirty()) : n.kind === "max" ? (n.inclusive ? e.data > n.value : e.data >= n.value) && (a = this._getOrReturnCtx(e, a), K(a, {
        code: z.too_big,
        type: "bigint",
        maximum: n.value,
        inclusive: n.inclusive,
        message: n.message
      }), t.dirty()) : n.kind === "multipleOf" ? e.data % n.value !== BigInt(0) && (a = this._getOrReturnCtx(e, a), K(a, {
        code: z.not_multiple_of,
        multipleOf: n.value,
        message: n.message
      }), t.dirty()) : ke.assertNever(n);
    return { status: t.value, value: e.data };
  }
  _getInvalidInput(e) {
    const r = this._getOrReturnCtx(e);
    return K(r, {
      code: z.invalid_type,
      expected: Y.bigint,
      received: r.parsedType
    }), oe;
  }
  gte(e, r) {
    return this.setLimit("min", e, !0, te.toString(r));
  }
  gt(e, r) {
    return this.setLimit("min", e, !1, te.toString(r));
  }
  lte(e, r) {
    return this.setLimit("max", e, !0, te.toString(r));
  }
  lt(e, r) {
    return this.setLimit("max", e, !1, te.toString(r));
  }
  setLimit(e, r, a, t) {
    return new Ar({
      ...this._def,
      checks: [
        ...this._def.checks,
        {
          kind: e,
          value: r,
          inclusive: a,
          message: te.toString(t)
        }
      ]
    });
  }
  _addCheck(e) {
    return new Ar({
      ...this._def,
      checks: [...this._def.checks, e]
    });
  }
  positive(e) {
    return this._addCheck({
      kind: "min",
      value: BigInt(0),
      inclusive: !1,
      message: te.toString(e)
    });
  }
  negative(e) {
    return this._addCheck({
      kind: "max",
      value: BigInt(0),
      inclusive: !1,
      message: te.toString(e)
    });
  }
  nonpositive(e) {
    return this._addCheck({
      kind: "max",
      value: BigInt(0),
      inclusive: !0,
      message: te.toString(e)
    });
  }
  nonnegative(e) {
    return this._addCheck({
      kind: "min",
      value: BigInt(0),
      inclusive: !0,
      message: te.toString(e)
    });
  }
  multipleOf(e, r) {
    return this._addCheck({
      kind: "multipleOf",
      value: e,
      message: te.toString(r)
    });
  }
  get minValue() {
    let e = null;
    for (const r of this._def.checks)
      r.kind === "min" && (e === null || r.value > e) && (e = r.value);
    return e;
  }
  get maxValue() {
    let e = null;
    for (const r of this._def.checks)
      r.kind === "max" && (e === null || r.value < e) && (e = r.value);
    return e;
  }
}
Ar.create = (s) => new Ar({
  checks: [],
  typeName: U.ZodBigInt,
  coerce: (s == null ? void 0 : s.coerce) ?? !1,
  ...ye(s)
});
class ws extends Pe {
  _parse(e) {
    if (this._def.coerce && (e.data = !!e.data), this._getType(e) !== Y.boolean) {
      const a = this._getOrReturnCtx(e);
      return K(a, {
        code: z.invalid_type,
        expected: Y.boolean,
        received: a.parsedType
      }), oe;
    }
    return bt(e.data);
  }
}
ws.create = (s) => new ws({
  typeName: U.ZodBoolean,
  coerce: (s == null ? void 0 : s.coerce) || !1,
  ...ye(s)
});
class Yr extends Pe {
  _parse(e) {
    if (this._def.coerce && (e.data = new Date(e.data)), this._getType(e) !== Y.date) {
      const n = this._getOrReturnCtx(e);
      return K(n, {
        code: z.invalid_type,
        expected: Y.date,
        received: n.parsedType
      }), oe;
    }
    if (Number.isNaN(e.data.getTime())) {
      const n = this._getOrReturnCtx(e);
      return K(n, {
        code: z.invalid_date
      }), oe;
    }
    const a = new ot();
    let t;
    for (const n of this._def.checks)
      n.kind === "min" ? e.data.getTime() < n.value && (t = this._getOrReturnCtx(e, t), K(t, {
        code: z.too_small,
        message: n.message,
        inclusive: !0,
        exact: !1,
        minimum: n.value,
        type: "date"
      }), a.dirty()) : n.kind === "max" ? e.data.getTime() > n.value && (t = this._getOrReturnCtx(e, t), K(t, {
        code: z.too_big,
        message: n.message,
        inclusive: !0,
        exact: !1,
        maximum: n.value,
        type: "date"
      }), a.dirty()) : ke.assertNever(n);
    return {
      status: a.value,
      value: new Date(e.data.getTime())
    };
  }
  _addCheck(e) {
    return new Yr({
      ...this._def,
      checks: [...this._def.checks, e]
    });
  }
  min(e, r) {
    return this._addCheck({
      kind: "min",
      value: e.getTime(),
      message: te.toString(r)
    });
  }
  max(e, r) {
    return this._addCheck({
      kind: "max",
      value: e.getTime(),
      message: te.toString(r)
    });
  }
  get minDate() {
    let e = null;
    for (const r of this._def.checks)
      r.kind === "min" && (e === null || r.value > e) && (e = r.value);
    return e != null ? new Date(e) : null;
  }
  get maxDate() {
    let e = null;
    for (const r of this._def.checks)
      r.kind === "max" && (e === null || r.value < e) && (e = r.value);
    return e != null ? new Date(e) : null;
  }
}
Yr.create = (s) => new Yr({
  checks: [],
  coerce: (s == null ? void 0 : s.coerce) || !1,
  typeName: U.ZodDate,
  ...ye(s)
});
class qn extends Pe {
  _parse(e) {
    if (this._getType(e) !== Y.symbol) {
      const a = this._getOrReturnCtx(e);
      return K(a, {
        code: z.invalid_type,
        expected: Y.symbol,
        received: a.parsedType
      }), oe;
    }
    return bt(e.data);
  }
}
qn.create = (s) => new qn({
  typeName: U.ZodSymbol,
  ...ye(s)
});
class xs extends Pe {
  _parse(e) {
    if (this._getType(e) !== Y.undefined) {
      const a = this._getOrReturnCtx(e);
      return K(a, {
        code: z.invalid_type,
        expected: Y.undefined,
        received: a.parsedType
      }), oe;
    }
    return bt(e.data);
  }
}
xs.create = (s) => new xs({
  typeName: U.ZodUndefined,
  ...ye(s)
});
class Es extends Pe {
  _parse(e) {
    if (this._getType(e) !== Y.null) {
      const a = this._getOrReturnCtx(e);
      return K(a, {
        code: z.invalid_type,
        expected: Y.null,
        received: a.parsedType
      }), oe;
    }
    return bt(e.data);
  }
}
Es.create = (s) => new Es({
  typeName: U.ZodNull,
  ...ye(s)
});
class Rs extends Pe {
  constructor() {
    super(...arguments), this._any = !0;
  }
  _parse(e) {
    return bt(e.data);
  }
}
Rs.create = (s) => new Rs({
  typeName: U.ZodAny,
  ...ye(s)
});
class ks extends Pe {
  constructor() {
    super(...arguments), this._unknown = !0;
  }
  _parse(e) {
    return bt(e.data);
  }
}
ks.create = (s) => new ks({
  typeName: U.ZodUnknown,
  ...ye(s)
});
class Yt extends Pe {
  _parse(e) {
    const r = this._getOrReturnCtx(e);
    return K(r, {
      code: z.invalid_type,
      expected: Y.never,
      received: r.parsedType
    }), oe;
  }
}
Yt.create = (s) => new Yt({
  typeName: U.ZodNever,
  ...ye(s)
});
class Ln extends Pe {
  _parse(e) {
    if (this._getType(e) !== Y.undefined) {
      const a = this._getOrReturnCtx(e);
      return K(a, {
        code: z.invalid_type,
        expected: Y.void,
        received: a.parsedType
      }), oe;
    }
    return bt(e.data);
  }
}
Ln.create = (s) => new Ln({
  typeName: U.ZodVoid,
  ...ye(s)
});
class Ct extends Pe {
  _parse(e) {
    const { ctx: r, status: a } = this._processInputParams(e), t = this._def;
    if (r.parsedType !== Y.array)
      return K(r, {
        code: z.invalid_type,
        expected: Y.array,
        received: r.parsedType
      }), oe;
    if (t.exactLength !== null) {
      const i = r.data.length > t.exactLength.value, o = r.data.length < t.exactLength.value;
      (i || o) && (K(r, {
        code: i ? z.too_big : z.too_small,
        minimum: o ? t.exactLength.value : void 0,
        maximum: i ? t.exactLength.value : void 0,
        type: "array",
        inclusive: !0,
        exact: !0,
        message: t.exactLength.message
      }), a.dirty());
    }
    if (t.minLength !== null && r.data.length < t.minLength.value && (K(r, {
      code: z.too_small,
      minimum: t.minLength.value,
      type: "array",
      inclusive: !0,
      exact: !1,
      message: t.minLength.message
    }), a.dirty()), t.maxLength !== null && r.data.length > t.maxLength.value && (K(r, {
      code: z.too_big,
      maximum: t.maxLength.value,
      type: "array",
      inclusive: !0,
      exact: !1,
      message: t.maxLength.message
    }), a.dirty()), r.common.async)
      return Promise.all([...r.data].map((i, o) => t.type._parseAsync(new It(r, i, r.path, o)))).then((i) => ot.mergeArray(a, i));
    const n = [...r.data].map((i, o) => t.type._parseSync(new It(r, i, r.path, o)));
    return ot.mergeArray(a, n);
  }
  get element() {
    return this._def.type;
  }
  min(e, r) {
    return new Ct({
      ...this._def,
      minLength: { value: e, message: te.toString(r) }
    });
  }
  max(e, r) {
    return new Ct({
      ...this._def,
      maxLength: { value: e, message: te.toString(r) }
    });
  }
  length(e, r) {
    return new Ct({
      ...this._def,
      exactLength: { value: e, message: te.toString(r) }
    });
  }
  nonempty(e) {
    return this.min(1, e);
  }
}
Ct.create = (s, e) => new Ct({
  type: s,
  minLength: null,
  maxLength: null,
  exactLength: null,
  typeName: U.ZodArray,
  ...ye(e)
});
function hr(s) {
  if (s instanceof Qe) {
    const e = {};
    for (const r in s.shape) {
      const a = s.shape[r];
      e[r] = Ut.create(hr(a));
    }
    return new Qe({
      ...s._def,
      shape: () => e
    });
  } else return s instanceof Ct ? new Ct({
    ...s._def,
    type: hr(s.element)
  }) : s instanceof Ut ? Ut.create(hr(s.unwrap())) : s instanceof ur ? ur.create(hr(s.unwrap())) : s instanceof nr ? nr.create(s.items.map((e) => hr(e))) : s;
}
class Qe extends Pe {
  constructor() {
    super(...arguments), this._cached = null, this.nonstrict = this.passthrough, this.augment = this.extend;
  }
  _getCached() {
    if (this._cached !== null)
      return this._cached;
    const e = this._def.shape(), r = ke.objectKeys(e);
    return this._cached = { shape: e, keys: r }, this._cached;
  }
  _parse(e) {
    if (this._getType(e) !== Y.object) {
      const l = this._getOrReturnCtx(e);
      return K(l, {
        code: z.invalid_type,
        expected: Y.object,
        received: l.parsedType
      }), oe;
    }
    const { status: a, ctx: t } = this._processInputParams(e), { shape: n, keys: i } = this._getCached(), o = [];
    if (!(this._def.catchall instanceof Yt && this._def.unknownKeys === "strip"))
      for (const l in t.data)
        i.includes(l) || o.push(l);
    const u = [];
    for (const l of i) {
      const h = n[l], m = t.data[l];
      u.push({
        key: { status: "valid", value: l },
        value: h._parse(new It(t, m, t.path, l)),
        alwaysSet: l in t.data
      });
    }
    if (this._def.catchall instanceof Yt) {
      const l = this._def.unknownKeys;
      if (l === "passthrough")
        for (const h of o)
          u.push({
            key: { status: "valid", value: h },
            value: { status: "valid", value: t.data[h] }
          });
      else if (l === "strict")
        o.length > 0 && (K(t, {
          code: z.unrecognized_keys,
          keys: o
        }), a.dirty());
      else if (l !== "strip") throw new Error("Internal ZodObject error: invalid unknownKeys value.");
    } else {
      const l = this._def.catchall;
      for (const h of o) {
        const m = t.data[h];
        u.push({
          key: { status: "valid", value: h },
          value: l._parse(
            new It(t, m, t.path, h)
            //, ctx.child(key), value, getParsedType(value)
          ),
          alwaysSet: h in t.data
        });
      }
    }
    return t.common.async ? Promise.resolve().then(async () => {
      const l = [];
      for (const h of u) {
        const m = await h.key, _ = await h.value;
        l.push({
          key: m,
          value: _,
          alwaysSet: h.alwaysSet
        });
      }
      return l;
    }).then((l) => ot.mergeObjectSync(a, l)) : ot.mergeObjectSync(a, u);
  }
  get shape() {
    return this._def.shape();
  }
  strict(e) {
    return te.errToObj, new Qe({
      ...this._def,
      unknownKeys: "strict",
      ...e !== void 0 ? {
        errorMap: (r, a) => {
          var n, i;
          const t = ((i = (n = this._def).errorMap) == null ? void 0 : i.call(n, r, a).message) ?? a.defaultError;
          return r.code === "unrecognized_keys" ? {
            message: te.errToObj(e).message ?? t
          } : {
            message: t
          };
        }
      } : {}
    });
  }
  strip() {
    return new Qe({
      ...this._def,
      unknownKeys: "strip"
    });
  }
  passthrough() {
    return new Qe({
      ...this._def,
      unknownKeys: "passthrough"
    });
  }
  // const AugmentFactory =
  //   <Def extends ZodObjectDef>(def: Def) =>
  //   <Augmentation extends ZodRawShape>(
  //     augmentation: Augmentation
  //   ): ZodObject<
  //     extendShape<ReturnType<Def["shape"]>, Augmentation>,
  //     Def["unknownKeys"],
  //     Def["catchall"]
  //   > => {
  //     return new ZodObject({
  //       ...def,
  //       shape: () => ({
  //         ...def.shape(),
  //         ...augmentation,
  //       }),
  //     }) as any;
  //   };
  extend(e) {
    return new Qe({
      ...this._def,
      shape: () => ({
        ...this._def.shape(),
        ...e
      })
    });
  }
  /**
   * Prior to zod@1.0.12 there was a bug in the
   * inferred type of merged objects. Please
   * upgrade if you are experiencing issues.
   */
  merge(e) {
    return new Qe({
      unknownKeys: e._def.unknownKeys,
      catchall: e._def.catchall,
      shape: () => ({
        ...this._def.shape(),
        ...e._def.shape()
      }),
      typeName: U.ZodObject
    });
  }
  // merge<
  //   Incoming extends AnyZodObject,
  //   Augmentation extends Incoming["shape"],
  //   NewOutput extends {
  //     [k in keyof Augmentation | keyof Output]: k extends keyof Augmentation
  //       ? Augmentation[k]["_output"]
  //       : k extends keyof Output
  //       ? Output[k]
  //       : never;
  //   },
  //   NewInput extends {
  //     [k in keyof Augmentation | keyof Input]: k extends keyof Augmentation
  //       ? Augmentation[k]["_input"]
  //       : k extends keyof Input
  //       ? Input[k]
  //       : never;
  //   }
  // >(
  //   merging: Incoming
  // ): ZodObject<
  //   extendShape<T, ReturnType<Incoming["_def"]["shape"]>>,
  //   Incoming["_def"]["unknownKeys"],
  //   Incoming["_def"]["catchall"],
  //   NewOutput,
  //   NewInput
  // > {
  //   const merged: any = new ZodObject({
  //     unknownKeys: merging._def.unknownKeys,
  //     catchall: merging._def.catchall,
  //     shape: () =>
  //       objectUtil.mergeShapes(this._def.shape(), merging._def.shape()),
  //     typeName: ZodFirstPartyTypeKind.ZodObject,
  //   }) as any;
  //   return merged;
  // }
  setKey(e, r) {
    return this.augment({ [e]: r });
  }
  // merge<Incoming extends AnyZodObject>(
  //   merging: Incoming
  // ): //ZodObject<T & Incoming["_shape"], UnknownKeys, Catchall> = (merging) => {
  // ZodObject<
  //   extendShape<T, ReturnType<Incoming["_def"]["shape"]>>,
  //   Incoming["_def"]["unknownKeys"],
  //   Incoming["_def"]["catchall"]
  // > {
  //   // const mergedShape = objectUtil.mergeShapes(
  //   //   this._def.shape(),
  //   //   merging._def.shape()
  //   // );
  //   const merged: any = new ZodObject({
  //     unknownKeys: merging._def.unknownKeys,
  //     catchall: merging._def.catchall,
  //     shape: () =>
  //       objectUtil.mergeShapes(this._def.shape(), merging._def.shape()),
  //     typeName: ZodFirstPartyTypeKind.ZodObject,
  //   }) as any;
  //   return merged;
  // }
  catchall(e) {
    return new Qe({
      ...this._def,
      catchall: e
    });
  }
  pick(e) {
    const r = {};
    for (const a of ke.objectKeys(e))
      e[a] && this.shape[a] && (r[a] = this.shape[a]);
    return new Qe({
      ...this._def,
      shape: () => r
    });
  }
  omit(e) {
    const r = {};
    for (const a of ke.objectKeys(this.shape))
      e[a] || (r[a] = this.shape[a]);
    return new Qe({
      ...this._def,
      shape: () => r
    });
  }
  /**
   * @deprecated
   */
  deepPartial() {
    return hr(this);
  }
  partial(e) {
    const r = {};
    for (const a of ke.objectKeys(this.shape)) {
      const t = this.shape[a];
      e && !e[a] ? r[a] = t : r[a] = t.optional();
    }
    return new Qe({
      ...this._def,
      shape: () => r
    });
  }
  required(e) {
    const r = {};
    for (const a of ke.objectKeys(this.shape))
      if (e && !e[a])
        r[a] = this.shape[a];
      else {
        let n = this.shape[a];
        for (; n instanceof Ut; )
          n = n._def.innerType;
        r[a] = n;
      }
    return new Qe({
      ...this._def,
      shape: () => r
    });
  }
  keyof() {
    return no(ke.objectKeys(this.shape));
  }
}
Qe.create = (s, e) => new Qe({
  shape: () => s,
  unknownKeys: "strip",
  catchall: Yt.create(),
  typeName: U.ZodObject,
  ...ye(e)
});
Qe.strictCreate = (s, e) => new Qe({
  shape: () => s,
  unknownKeys: "strict",
  catchall: Yt.create(),
  typeName: U.ZodObject,
  ...ye(e)
});
Qe.lazycreate = (s, e) => new Qe({
  shape: s,
  unknownKeys: "strip",
  catchall: Yt.create(),
  typeName: U.ZodObject,
  ...ye(e)
});
class Xr extends Pe {
  _parse(e) {
    const { ctx: r } = this._processInputParams(e), a = this._def.options;
    function t(n) {
      for (const o of n)
        if (o.result.status === "valid")
          return o.result;
      for (const o of n)
        if (o.result.status === "dirty")
          return r.common.issues.push(...o.ctx.common.issues), o.result;
      const i = n.map((o) => new Bt(o.ctx.common.issues));
      return K(r, {
        code: z.invalid_union,
        unionErrors: i
      }), oe;
    }
    if (r.common.async)
      return Promise.all(a.map(async (n) => {
        const i = {
          ...r,
          common: {
            ...r.common,
            issues: []
          },
          parent: null
        };
        return {
          result: await n._parseAsync({
            data: r.data,
            path: r.path,
            parent: i
          }),
          ctx: i
        };
      })).then(t);
    {
      let n;
      const i = [];
      for (const u of a) {
        const l = {
          ...r,
          common: {
            ...r.common,
            issues: []
          },
          parent: null
        }, h = u._parseSync({
          data: r.data,
          path: r.path,
          parent: l
        });
        if (h.status === "valid")
          return h;
        h.status === "dirty" && !n && (n = { result: h, ctx: l }), l.common.issues.length && i.push(l.common.issues);
      }
      if (n)
        return r.common.issues.push(...n.ctx.common.issues), n.result;
      const o = i.map((u) => new Bt(u));
      return K(r, {
        code: z.invalid_union,
        unionErrors: o
      }), oe;
    }
  }
  get options() {
    return this._def.options;
  }
}
Xr.create = (s, e) => new Xr({
  options: s,
  typeName: U.ZodUnion,
  ...ye(e)
});
const Lt = (s) => s instanceof As ? Lt(s.schema) : s instanceof or ? Lt(s.innerType()) : s instanceof ra ? [s.value] : s instanceof ir ? s.options : s instanceof Os ? ke.objectValues(s.enum) : s instanceof sa ? Lt(s._def.innerType) : s instanceof xs ? [void 0] : s instanceof Es ? [null] : s instanceof Ut ? [void 0, ...Lt(s.unwrap())] : s instanceof ur ? [null, ...Lt(s.unwrap())] : s instanceof io || s instanceof ia ? Lt(s.unwrap()) : s instanceof na ? Lt(s._def.innerType) : [];
class Ks extends Pe {
  _parse(e) {
    const { ctx: r } = this._processInputParams(e);
    if (r.parsedType !== Y.object)
      return K(r, {
        code: z.invalid_type,
        expected: Y.object,
        received: r.parsedType
      }), oe;
    const a = this.discriminator, t = r.data[a], n = this.optionsMap.get(t);
    return n ? r.common.async ? n._parseAsync({
      data: r.data,
      path: r.path,
      parent: r
    }) : n._parseSync({
      data: r.data,
      path: r.path,
      parent: r
    }) : (K(r, {
      code: z.invalid_union_discriminator,
      options: Array.from(this.optionsMap.keys()),
      path: [a]
    }), oe);
  }
  get discriminator() {
    return this._def.discriminator;
  }
  get options() {
    return this._def.options;
  }
  get optionsMap() {
    return this._def.optionsMap;
  }
  /**
   * The constructor of the discriminated union schema. Its behaviour is very similar to that of the normal z.union() constructor.
   * However, it only allows a union of objects, all of which need to share a discriminator property. This property must
   * have a different value for each object in the union.
   * @param discriminator the name of the discriminator property
   * @param types an array of object schemas
   * @param params
   */
  static create(e, r, a) {
    const t = /* @__PURE__ */ new Map();
    for (const n of r) {
      const i = Lt(n.shape[e]);
      if (!i.length)
        throw new Error(`A discriminator value for key \`${e}\` could not be extracted from all schema options`);
      for (const o of i) {
        if (t.has(o))
          throw new Error(`Discriminator property ${String(e)} has duplicate value ${String(o)}`);
        t.set(o, n);
      }
    }
    return new Ks({
      typeName: U.ZodDiscriminatedUnion,
      discriminator: e,
      options: r,
      optionsMap: t,
      ...ye(a)
    });
  }
}
function Ts(s, e) {
  const r = Wt(s), a = Wt(e);
  if (s === e)
    return { valid: !0, data: s };
  if (r === Y.object && a === Y.object) {
    const t = ke.objectKeys(e), n = ke.objectKeys(s).filter((o) => t.indexOf(o) !== -1), i = { ...s, ...e };
    for (const o of n) {
      const u = Ts(s[o], e[o]);
      if (!u.valid)
        return { valid: !1 };
      i[o] = u.data;
    }
    return { valid: !0, data: i };
  } else if (r === Y.array && a === Y.array) {
    if (s.length !== e.length)
      return { valid: !1 };
    const t = [];
    for (let n = 0; n < s.length; n++) {
      const i = s[n], o = e[n], u = Ts(i, o);
      if (!u.valid)
        return { valid: !1 };
      t.push(u.data);
    }
    return { valid: !0, data: t };
  } else return r === Y.date && a === Y.date && +s == +e ? { valid: !0, data: s } : { valid: !1 };
}
class ea extends Pe {
  _parse(e) {
    const { status: r, ctx: a } = this._processInputParams(e), t = (n, i) => {
      if (Dn(n) || Dn(i))
        return oe;
      const o = Ts(n.value, i.value);
      return o.valid ? ((jn(n) || jn(i)) && r.dirty(), { status: r.value, value: o.data }) : (K(a, {
        code: z.invalid_intersection_types
      }), oe);
    };
    return a.common.async ? Promise.all([
      this._def.left._parseAsync({
        data: a.data,
        path: a.path,
        parent: a
      }),
      this._def.right._parseAsync({
        data: a.data,
        path: a.path,
        parent: a
      })
    ]).then(([n, i]) => t(n, i)) : t(this._def.left._parseSync({
      data: a.data,
      path: a.path,
      parent: a
    }), this._def.right._parseSync({
      data: a.data,
      path: a.path,
      parent: a
    }));
  }
}
ea.create = (s, e, r) => new ea({
  left: s,
  right: e,
  typeName: U.ZodIntersection,
  ...ye(r)
});
class nr extends Pe {
  _parse(e) {
    const { status: r, ctx: a } = this._processInputParams(e);
    if (a.parsedType !== Y.array)
      return K(a, {
        code: z.invalid_type,
        expected: Y.array,
        received: a.parsedType
      }), oe;
    if (a.data.length < this._def.items.length)
      return K(a, {
        code: z.too_small,
        minimum: this._def.items.length,
        inclusive: !0,
        exact: !1,
        type: "array"
      }), oe;
    !this._def.rest && a.data.length > this._def.items.length && (K(a, {
      code: z.too_big,
      maximum: this._def.items.length,
      inclusive: !0,
      exact: !1,
      type: "array"
    }), r.dirty());
    const n = [...a.data].map((i, o) => {
      const u = this._def.items[o] || this._def.rest;
      return u ? u._parse(new It(a, i, a.path, o)) : null;
    }).filter((i) => !!i);
    return a.common.async ? Promise.all(n).then((i) => ot.mergeArray(r, i)) : ot.mergeArray(r, n);
  }
  get items() {
    return this._def.items;
  }
  rest(e) {
    return new nr({
      ...this._def,
      rest: e
    });
  }
}
nr.create = (s, e) => {
  if (!Array.isArray(s))
    throw new Error("You must pass an array of schemas to z.tuple([ ... ])");
  return new nr({
    items: s,
    typeName: U.ZodTuple,
    rest: null,
    ...ye(e)
  });
};
class ta extends Pe {
  get keySchema() {
    return this._def.keyType;
  }
  get valueSchema() {
    return this._def.valueType;
  }
  _parse(e) {
    const { status: r, ctx: a } = this._processInputParams(e);
    if (a.parsedType !== Y.object)
      return K(a, {
        code: z.invalid_type,
        expected: Y.object,
        received: a.parsedType
      }), oe;
    const t = [], n = this._def.keyType, i = this._def.valueType;
    for (const o in a.data)
      t.push({
        key: n._parse(new It(a, o, a.path, o)),
        value: i._parse(new It(a, a.data[o], a.path, o)),
        alwaysSet: o in a.data
      });
    return a.common.async ? ot.mergeObjectAsync(r, t) : ot.mergeObjectSync(r, t);
  }
  get element() {
    return this._def.valueType;
  }
  static create(e, r, a) {
    return r instanceof Pe ? new ta({
      keyType: e,
      valueType: r,
      typeName: U.ZodRecord,
      ...ye(a)
    }) : new ta({
      keyType: Mt.create(),
      valueType: e,
      typeName: U.ZodRecord,
      ...ye(r)
    });
  }
}
class Zn extends Pe {
  get keySchema() {
    return this._def.keyType;
  }
  get valueSchema() {
    return this._def.valueType;
  }
  _parse(e) {
    const { status: r, ctx: a } = this._processInputParams(e);
    if (a.parsedType !== Y.map)
      return K(a, {
        code: z.invalid_type,
        expected: Y.map,
        received: a.parsedType
      }), oe;
    const t = this._def.keyType, n = this._def.valueType, i = [...a.data.entries()].map(([o, u], l) => ({
      key: t._parse(new It(a, o, a.path, [l, "key"])),
      value: n._parse(new It(a, u, a.path, [l, "value"]))
    }));
    if (a.common.async) {
      const o = /* @__PURE__ */ new Map();
      return Promise.resolve().then(async () => {
        for (const u of i) {
          const l = await u.key, h = await u.value;
          if (l.status === "aborted" || h.status === "aborted")
            return oe;
          (l.status === "dirty" || h.status === "dirty") && r.dirty(), o.set(l.value, h.value);
        }
        return { status: r.value, value: o };
      });
    } else {
      const o = /* @__PURE__ */ new Map();
      for (const u of i) {
        const l = u.key, h = u.value;
        if (l.status === "aborted" || h.status === "aborted")
          return oe;
        (l.status === "dirty" || h.status === "dirty") && r.dirty(), o.set(l.value, h.value);
      }
      return { status: r.value, value: o };
    }
  }
}
Zn.create = (s, e, r) => new Zn({
  valueType: e,
  keyType: s,
  typeName: U.ZodMap,
  ...ye(r)
});
class Or extends Pe {
  _parse(e) {
    const { status: r, ctx: a } = this._processInputParams(e);
    if (a.parsedType !== Y.set)
      return K(a, {
        code: z.invalid_type,
        expected: Y.set,
        received: a.parsedType
      }), oe;
    const t = this._def;
    t.minSize !== null && a.data.size < t.minSize.value && (K(a, {
      code: z.too_small,
      minimum: t.minSize.value,
      type: "set",
      inclusive: !0,
      exact: !1,
      message: t.minSize.message
    }), r.dirty()), t.maxSize !== null && a.data.size > t.maxSize.value && (K(a, {
      code: z.too_big,
      maximum: t.maxSize.value,
      type: "set",
      inclusive: !0,
      exact: !1,
      message: t.maxSize.message
    }), r.dirty());
    const n = this._def.valueType;
    function i(u) {
      const l = /* @__PURE__ */ new Set();
      for (const h of u) {
        if (h.status === "aborted")
          return oe;
        h.status === "dirty" && r.dirty(), l.add(h.value);
      }
      return { status: r.value, value: l };
    }
    const o = [...a.data.values()].map((u, l) => n._parse(new It(a, u, a.path, l)));
    return a.common.async ? Promise.all(o).then((u) => i(u)) : i(o);
  }
  min(e, r) {
    return new Or({
      ...this._def,
      minSize: { value: e, message: te.toString(r) }
    });
  }
  max(e, r) {
    return new Or({
      ...this._def,
      maxSize: { value: e, message: te.toString(r) }
    });
  }
  size(e, r) {
    return this.min(e, r).max(e, r);
  }
  nonempty(e) {
    return this.min(1, e);
  }
}
Or.create = (s, e) => new Or({
  valueType: s,
  minSize: null,
  maxSize: null,
  typeName: U.ZodSet,
  ...ye(e)
});
class As extends Pe {
  get schema() {
    return this._def.getter();
  }
  _parse(e) {
    const { ctx: r } = this._processInputParams(e);
    return this._def.getter()._parse({ data: r.data, path: r.path, parent: r });
  }
}
As.create = (s, e) => new As({
  getter: s,
  typeName: U.ZodLazy,
  ...ye(e)
});
class ra extends Pe {
  _parse(e) {
    if (e.data !== this._def.value) {
      const r = this._getOrReturnCtx(e);
      return K(r, {
        received: r.data,
        code: z.invalid_literal,
        expected: this._def.value
      }), oe;
    }
    return { status: "valid", value: e.data };
  }
  get value() {
    return this._def.value;
  }
}
ra.create = (s, e) => new ra({
  value: s,
  typeName: U.ZodLiteral,
  ...ye(e)
});
function no(s, e) {
  return new ir({
    values: s,
    typeName: U.ZodEnum,
    ...ye(e)
  });
}
class ir extends Pe {
  _parse(e) {
    if (typeof e.data != "string") {
      const r = this._getOrReturnCtx(e), a = this._def.values;
      return K(r, {
        expected: ke.joinValues(a),
        received: r.parsedType,
        code: z.invalid_type
      }), oe;
    }
    if (this._cache || (this._cache = new Set(this._def.values)), !this._cache.has(e.data)) {
      const r = this._getOrReturnCtx(e), a = this._def.values;
      return K(r, {
        received: r.data,
        code: z.invalid_enum_value,
        options: a
      }), oe;
    }
    return bt(e.data);
  }
  get options() {
    return this._def.values;
  }
  get enum() {
    const e = {};
    for (const r of this._def.values)
      e[r] = r;
    return e;
  }
  get Values() {
    const e = {};
    for (const r of this._def.values)
      e[r] = r;
    return e;
  }
  get Enum() {
    const e = {};
    for (const r of this._def.values)
      e[r] = r;
    return e;
  }
  extract(e, r = this._def) {
    return ir.create(e, {
      ...this._def,
      ...r
    });
  }
  exclude(e, r = this._def) {
    return ir.create(this.options.filter((a) => !e.includes(a)), {
      ...this._def,
      ...r
    });
  }
}
ir.create = no;
class Os extends Pe {
  _parse(e) {
    const r = ke.getValidEnumValues(this._def.values), a = this._getOrReturnCtx(e);
    if (a.parsedType !== Y.string && a.parsedType !== Y.number) {
      const t = ke.objectValues(r);
      return K(a, {
        expected: ke.joinValues(t),
        received: a.parsedType,
        code: z.invalid_type
      }), oe;
    }
    if (this._cache || (this._cache = new Set(ke.getValidEnumValues(this._def.values))), !this._cache.has(e.data)) {
      const t = ke.objectValues(r);
      return K(a, {
        received: a.data,
        code: z.invalid_enum_value,
        options: t
      }), oe;
    }
    return bt(e.data);
  }
  get enum() {
    return this._def.values;
  }
}
Os.create = (s, e) => new Os({
  values: s,
  typeName: U.ZodNativeEnum,
  ...ye(e)
});
class aa extends Pe {
  unwrap() {
    return this._def.type;
  }
  _parse(e) {
    const { ctx: r } = this._processInputParams(e);
    if (r.parsedType !== Y.promise && r.common.async === !1)
      return K(r, {
        code: z.invalid_type,
        expected: Y.promise,
        received: r.parsedType
      }), oe;
    const a = r.parsedType === Y.promise ? r.data : Promise.resolve(r.data);
    return bt(a.then((t) => this._def.type.parseAsync(t, {
      path: r.path,
      errorMap: r.common.contextualErrorMap
    })));
  }
}
aa.create = (s, e) => new aa({
  type: s,
  typeName: U.ZodPromise,
  ...ye(e)
});
class or extends Pe {
  innerType() {
    return this._def.schema;
  }
  sourceType() {
    return this._def.schema._def.typeName === U.ZodEffects ? this._def.schema.sourceType() : this._def.schema;
  }
  _parse(e) {
    const { status: r, ctx: a } = this._processInputParams(e), t = this._def.effect || null, n = {
      addIssue: (i) => {
        K(a, i), i.fatal ? r.abort() : r.dirty();
      },
      get path() {
        return a.path;
      }
    };
    if (n.addIssue = n.addIssue.bind(n), t.type === "preprocess") {
      const i = t.transform(a.data, n);
      if (a.common.async)
        return Promise.resolve(i).then(async (o) => {
          if (r.value === "aborted")
            return oe;
          const u = await this._def.schema._parseAsync({
            data: o,
            path: a.path,
            parent: a
          });
          return u.status === "aborted" ? oe : u.status === "dirty" || r.value === "dirty" ? wr(u.value) : u;
        });
      {
        if (r.value === "aborted")
          return oe;
        const o = this._def.schema._parseSync({
          data: i,
          path: a.path,
          parent: a
        });
        return o.status === "aborted" ? oe : o.status === "dirty" || r.value === "dirty" ? wr(o.value) : o;
      }
    }
    if (t.type === "refinement") {
      const i = (o) => {
        const u = t.refinement(o, n);
        if (a.common.async)
          return Promise.resolve(u);
        if (u instanceof Promise)
          throw new Error("Async refinement encountered during synchronous parse operation. Use .parseAsync instead.");
        return o;
      };
      if (a.common.async === !1) {
        const o = this._def.schema._parseSync({
          data: a.data,
          path: a.path,
          parent: a
        });
        return o.status === "aborted" ? oe : (o.status === "dirty" && r.dirty(), i(o.value), { status: r.value, value: o.value });
      } else
        return this._def.schema._parseAsync({ data: a.data, path: a.path, parent: a }).then((o) => o.status === "aborted" ? oe : (o.status === "dirty" && r.dirty(), i(o.value).then(() => ({ status: r.value, value: o.value }))));
    }
    if (t.type === "transform")
      if (a.common.async === !1) {
        const i = this._def.schema._parseSync({
          data: a.data,
          path: a.path,
          parent: a
        });
        if (!mr(i))
          return oe;
        const o = t.transform(i.value, n);
        if (o instanceof Promise)
          throw new Error("Asynchronous transform encountered during synchronous parse operation. Use .parseAsync instead.");
        return { status: r.value, value: o };
      } else
        return this._def.schema._parseAsync({ data: a.data, path: a.path, parent: a }).then((i) => mr(i) ? Promise.resolve(t.transform(i.value, n)).then((o) => ({
          status: r.value,
          value: o
        })) : oe);
    ke.assertNever(t);
  }
}
or.create = (s, e, r) => new or({
  schema: s,
  typeName: U.ZodEffects,
  effect: e,
  ...ye(r)
});
or.createWithPreprocess = (s, e, r) => new or({
  schema: e,
  effect: { type: "preprocess", transform: s },
  typeName: U.ZodEffects,
  ...ye(r)
});
class Ut extends Pe {
  _parse(e) {
    return this._getType(e) === Y.undefined ? bt(void 0) : this._def.innerType._parse(e);
  }
  unwrap() {
    return this._def.innerType;
  }
}
Ut.create = (s, e) => new Ut({
  innerType: s,
  typeName: U.ZodOptional,
  ...ye(e)
});
class ur extends Pe {
  _parse(e) {
    return this._getType(e) === Y.null ? bt(null) : this._def.innerType._parse(e);
  }
  unwrap() {
    return this._def.innerType;
  }
}
ur.create = (s, e) => new ur({
  innerType: s,
  typeName: U.ZodNullable,
  ...ye(e)
});
class sa extends Pe {
  _parse(e) {
    const { ctx: r } = this._processInputParams(e);
    let a = r.data;
    return r.parsedType === Y.undefined && (a = this._def.defaultValue()), this._def.innerType._parse({
      data: a,
      path: r.path,
      parent: r
    });
  }
  removeDefault() {
    return this._def.innerType;
  }
}
sa.create = (s, e) => new sa({
  innerType: s,
  typeName: U.ZodDefault,
  defaultValue: typeof e.default == "function" ? e.default : () => e.default,
  ...ye(e)
});
class na extends Pe {
  _parse(e) {
    const { ctx: r } = this._processInputParams(e), a = {
      ...r,
      common: {
        ...r.common,
        issues: []
      }
    }, t = this._def.innerType._parse({
      data: a.data,
      path: a.path,
      parent: {
        ...a
      }
    });
    return Gr(t) ? t.then((n) => ({
      status: "valid",
      value: n.status === "valid" ? n.value : this._def.catchValue({
        get error() {
          return new Bt(a.common.issues);
        },
        input: a.data
      })
    })) : {
      status: "valid",
      value: t.status === "valid" ? t.value : this._def.catchValue({
        get error() {
          return new Bt(a.common.issues);
        },
        input: a.data
      })
    };
  }
  removeCatch() {
    return this._def.innerType;
  }
}
na.create = (s, e) => new na({
  innerType: s,
  typeName: U.ZodCatch,
  catchValue: typeof e.catch == "function" ? e.catch : () => e.catch,
  ...ye(e)
});
class Mn extends Pe {
  _parse(e) {
    if (this._getType(e) !== Y.nan) {
      const a = this._getOrReturnCtx(e);
      return K(a, {
        code: z.invalid_type,
        expected: Y.nan,
        received: a.parsedType
      }), oe;
    }
    return { status: "valid", value: e.data };
  }
}
Mn.create = (s) => new Mn({
  typeName: U.ZodNaN,
  ...ye(s)
});
class io extends Pe {
  _parse(e) {
    const { ctx: r } = this._processInputParams(e), a = r.data;
    return this._def.type._parse({
      data: a,
      path: r.path,
      parent: r
    });
  }
  unwrap() {
    return this._def.type;
  }
}
class Js extends Pe {
  _parse(e) {
    const { status: r, ctx: a } = this._processInputParams(e);
    if (a.common.async)
      return (async () => {
        const n = await this._def.in._parseAsync({
          data: a.data,
          path: a.path,
          parent: a
        });
        return n.status === "aborted" ? oe : n.status === "dirty" ? (r.dirty(), wr(n.value)) : this._def.out._parseAsync({
          data: n.value,
          path: a.path,
          parent: a
        });
      })();
    {
      const t = this._def.in._parseSync({
        data: a.data,
        path: a.path,
        parent: a
      });
      return t.status === "aborted" ? oe : t.status === "dirty" ? (r.dirty(), {
        status: "dirty",
        value: t.value
      }) : this._def.out._parseSync({
        data: t.value,
        path: a.path,
        parent: a
      });
    }
  }
  static create(e, r) {
    return new Js({
      in: e,
      out: r,
      typeName: U.ZodPipeline
    });
  }
}
class ia extends Pe {
  _parse(e) {
    const r = this._def.innerType._parse(e), a = (t) => (mr(t) && (t.value = Object.freeze(t.value)), t);
    return Gr(r) ? r.then((t) => a(t)) : a(r);
  }
  unwrap() {
    return this._def.innerType;
  }
}
ia.create = (s, e) => new ia({
  innerType: s,
  typeName: U.ZodReadonly,
  ...ye(e)
});
var U;
(function(s) {
  s.ZodString = "ZodString", s.ZodNumber = "ZodNumber", s.ZodNaN = "ZodNaN", s.ZodBigInt = "ZodBigInt", s.ZodBoolean = "ZodBoolean", s.ZodDate = "ZodDate", s.ZodSymbol = "ZodSymbol", s.ZodUndefined = "ZodUndefined", s.ZodNull = "ZodNull", s.ZodAny = "ZodAny", s.ZodUnknown = "ZodUnknown", s.ZodNever = "ZodNever", s.ZodVoid = "ZodVoid", s.ZodArray = "ZodArray", s.ZodObject = "ZodObject", s.ZodUnion = "ZodUnion", s.ZodDiscriminatedUnion = "ZodDiscriminatedUnion", s.ZodIntersection = "ZodIntersection", s.ZodTuple = "ZodTuple", s.ZodRecord = "ZodRecord", s.ZodMap = "ZodMap", s.ZodSet = "ZodSet", s.ZodFunction = "ZodFunction", s.ZodLazy = "ZodLazy", s.ZodLiteral = "ZodLiteral", s.ZodEnum = "ZodEnum", s.ZodEffects = "ZodEffects", s.ZodNativeEnum = "ZodNativeEnum", s.ZodOptional = "ZodOptional", s.ZodNullable = "ZodNullable", s.ZodDefault = "ZodDefault", s.ZodCatch = "ZodCatch", s.ZodPromise = "ZodPromise", s.ZodBranded = "ZodBranded", s.ZodPipeline = "ZodPipeline", s.ZodReadonly = "ZodReadonly";
})(U || (U = {}));
const H = Mt.create, et = vr.create, ct = ws.create, Gl = Rs.create, Nr = ks.create;
Yt.create;
const Ke = Ct.create, W = Qe.create, ut = Xr.create, Yl = Ks.create;
ea.create;
nr.create;
const gr = ta.create, me = ra.create, Nt = ir.create;
aa.create;
const q = Ut.create;
ur.create;
const oo = "2025-06-18", Xl = [
  oo,
  "2025-03-26",
  "2024-11-05",
  "2024-10-07"
], ha = "2.0", uo = ut([H(), et().int()]), lo = H(), ec = W({
  /**
   * If specified, the caller is requesting out-of-band progress notifications for this request (as represented by notifications/progress). The value of this parameter is an opaque token that will be attached to any subsequent notifications. The receiver is not obligated to provide these notifications.
   */
  progressToken: q(uo)
}).passthrough(), Pt = W({
  _meta: q(ec)
}).passthrough(), ft = W({
  method: H(),
  params: q(Pt)
}), Dr = W({
  /**
   * See [MCP specification](https://github.com/modelcontextprotocol/modelcontextprotocol/blob/47339c03c143bb4ec01a26e721a1b8fe66634ebe/docs/specification/draft/basic/index.mdx#general-fields)
   * for notes on _meta usage.
   */
  _meta: q(W({}).passthrough())
}).passthrough(), jt = W({
  method: H(),
  params: q(Dr)
}), St = W({
  /**
   * See [MCP specification](https://github.com/modelcontextprotocol/modelcontextprotocol/blob/47339c03c143bb4ec01a26e721a1b8fe66634ebe/docs/specification/draft/basic/index.mdx#general-fields)
   * for notes on _meta usage.
   */
  _meta: q(W({}).passthrough())
}).passthrough(), fa = ut([H(), et().int()]), co = W({
  jsonrpc: me(ha),
  id: fa
}).merge(ft).strict(), tc = (s) => co.safeParse(s).success, ho = W({
  jsonrpc: me(ha)
}).merge(jt).strict(), rc = (s) => ho.safeParse(s).success, fo = W({
  jsonrpc: me(ha),
  id: fa,
  result: St
}).strict(), zn = (s) => fo.safeParse(s).success;
var qe;
(function(s) {
  s[s.ConnectionClosed = -32e3] = "ConnectionClosed", s[s.RequestTimeout = -32001] = "RequestTimeout", s[s.ParseError = -32700] = "ParseError", s[s.InvalidRequest = -32600] = "InvalidRequest", s[s.MethodNotFound = -32601] = "MethodNotFound", s[s.InvalidParams = -32602] = "InvalidParams", s[s.InternalError = -32603] = "InternalError";
})(qe || (qe = {}));
const po = W({
  jsonrpc: me(ha),
  id: fa,
  error: W({
    /**
     * The error type that occurred.
     */
    code: et().int(),
    /**
     * A short description of the error. The message SHOULD be limited to a concise single sentence.
     */
    message: H(),
    /**
     * Additional information about the error. The value of this member is defined by the sender (e.g. detailed error information, nested errors etc.).
     */
    data: q(Nr())
  })
}).strict(), ac = (s) => po.safeParse(s).success;
ut([
  co,
  ho,
  fo,
  po
]);
const Ws = St.strict(), Gs = jt.extend({
  method: me("notifications/cancelled"),
  params: Dr.extend({
    /**
     * The ID of the request to cancel.
     *
     * This MUST correspond to the ID of a request previously issued in the same direction.
     */
    requestId: fa,
    /**
     * An optional string describing the reason for the cancellation. This MAY be logged or presented to the user.
     */
    reason: H().optional()
  })
}), jr = W({
  /** Intended for programmatic or logical use, but used as a display name in past specs or fallback */
  name: H(),
  /**
  * Intended for UI and end-user contexts — optimized to be human-readable and easily understood,
  * even by those unfamiliar with domain-specific terminology.
  *
  * If not provided, the name should be used for display (except for Tool,
  * where `annotations.title` should be given precedence over using `name`,
  * if present).
  */
  title: q(H())
}).passthrough(), mo = jr.extend({
  version: H()
}), sc = W({
  /**
   * Experimental, non-standard capabilities that the client supports.
   */
  experimental: q(W({}).passthrough()),
  /**
   * Present if the client supports sampling from an LLM.
   */
  sampling: q(W({}).passthrough()),
  /**
   * Present if the client supports eliciting user input.
   */
  elicitation: q(W({}).passthrough()),
  /**
   * Present if the client supports listing roots.
   */
  roots: q(W({
    /**
     * Whether the client supports issuing notifications for changes to the roots list.
     */
    listChanged: q(ct())
  }).passthrough())
}).passthrough(), vo = ft.extend({
  method: me("initialize"),
  params: Pt.extend({
    /**
     * The latest version of the Model Context Protocol that the client supports. The client MAY decide to support older versions as well.
     */
    protocolVersion: H(),
    capabilities: sc,
    clientInfo: mo
  })
}), nc = W({
  /**
   * Experimental, non-standard capabilities that the server supports.
   */
  experimental: q(W({}).passthrough()),
  /**
   * Present if the server supports sending log messages to the client.
   */
  logging: q(W({}).passthrough()),
  /**
   * Present if the server supports sending completions to the client.
   */
  completions: q(W({}).passthrough()),
  /**
   * Present if the server offers any prompt templates.
   */
  prompts: q(W({
    /**
     * Whether this server supports issuing notifications for changes to the prompt list.
     */
    listChanged: q(ct())
  }).passthrough()),
  /**
   * Present if the server offers any resources to read.
   */
  resources: q(W({
    /**
     * Whether this server supports clients subscribing to resource updates.
     */
    subscribe: q(ct()),
    /**
     * Whether this server supports issuing notifications for changes to the resource list.
     */
    listChanged: q(ct())
  }).passthrough()),
  /**
   * Present if the server offers any tools to call.
   */
  tools: q(W({
    /**
     * Whether this server supports issuing notifications for changes to the tool list.
     */
    listChanged: q(ct())
  }).passthrough())
}).passthrough(), ic = St.extend({
  /**
   * The version of the Model Context Protocol that the server wants to use. This may not match the version that the client requested. If the client cannot support this version, it MUST disconnect.
   */
  protocolVersion: H(),
  capabilities: nc,
  serverInfo: mo,
  /**
   * Instructions describing how to use the server and its features.
   *
   * This can be used by clients to improve the LLM's understanding of available tools, resources, etc. It can be thought of like a "hint" to the model. For example, this information MAY be added to the system prompt.
   */
  instructions: q(H())
}), go = jt.extend({
  method: me("notifications/initialized")
}), Ys = ft.extend({
  method: me("ping")
}), oc = W({
  /**
   * The progress thus far. This should increase every time progress is made, even if the total is unknown.
   */
  progress: et(),
  /**
   * Total number of items to process (or total progress required), if known.
   */
  total: q(et()),
  /**
   * An optional message describing the current progress.
   */
  message: q(H())
}).passthrough(), Xs = jt.extend({
  method: me("notifications/progress"),
  params: Dr.merge(oc).extend({
    /**
     * The progress token which was given in the initial request, used to associate this notification with the request that is proceeding.
     */
    progressToken: uo
  })
}), pa = ft.extend({
  params: Pt.extend({
    /**
     * An opaque token representing the current pagination position.
     * If provided, the server should return results starting after this cursor.
     */
    cursor: q(lo)
  }).optional()
}), ma = St.extend({
  /**
   * An opaque token representing the pagination position after the last returned result.
   * If present, there may be more results available.
   */
  nextCursor: q(lo)
}), yo = W({
  /**
   * The URI of this resource.
   */
  uri: H(),
  /**
   * The MIME type of this resource, if known.
   */
  mimeType: q(H()),
  /**
   * See [MCP specification](https://github.com/modelcontextprotocol/modelcontextprotocol/blob/47339c03c143bb4ec01a26e721a1b8fe66634ebe/docs/specification/draft/basic/index.mdx#general-fields)
   * for notes on _meta usage.
   */
  _meta: q(W({}).passthrough())
}).passthrough(), _o = yo.extend({
  /**
   * The text of the item. This must only be set if the item can actually be represented as text (not binary data).
   */
  text: H()
}), en = H().refine((s) => {
  try {
    return atob(s), !0;
  } catch {
    return !1;
  }
}, { message: "Invalid Base64 string" }), bo = yo.extend({
  /**
   * A base64-encoded string representing the binary data of the item.
   */
  blob: en
}), Po = jr.extend({
  /**
   * The URI of this resource.
   */
  uri: H(),
  /**
   * A description of what this resource represents.
   *
   * This can be used by clients to improve the LLM's understanding of available resources. It can be thought of like a "hint" to the model.
   */
  description: q(H()),
  /**
   * The MIME type of this resource, if known.
   */
  mimeType: q(H()),
  /**
   * See [MCP specification](https://github.com/modelcontextprotocol/modelcontextprotocol/blob/47339c03c143bb4ec01a26e721a1b8fe66634ebe/docs/specification/draft/basic/index.mdx#general-fields)
   * for notes on _meta usage.
   */
  _meta: q(W({}).passthrough())
}), uc = jr.extend({
  /**
   * A URI template (according to RFC 6570) that can be used to construct resource URIs.
   */
  uriTemplate: H(),
  /**
   * A description of what this template is for.
   *
   * This can be used by clients to improve the LLM's understanding of available resources. It can be thought of like a "hint" to the model.
   */
  description: q(H()),
  /**
   * The MIME type for all resources that match this template. This should only be included if all resources matching this template have the same type.
   */
  mimeType: q(H()),
  /**
   * See [MCP specification](https://github.com/modelcontextprotocol/modelcontextprotocol/blob/47339c03c143bb4ec01a26e721a1b8fe66634ebe/docs/specification/draft/basic/index.mdx#general-fields)
   * for notes on _meta usage.
   */
  _meta: q(W({}).passthrough())
}), Cs = pa.extend({
  method: me("resources/list")
}), lc = ma.extend({
  resources: Ke(Po)
}), $s = pa.extend({
  method: me("resources/templates/list")
}), cc = ma.extend({
  resourceTemplates: Ke(uc)
}), Is = ft.extend({
  method: me("resources/read"),
  params: Pt.extend({
    /**
     * The URI of the resource to read. The URI can use any protocol; it is up to the server how to interpret it.
     */
    uri: H()
  })
}), dc = St.extend({
  contents: Ke(ut([_o, bo]))
}), hc = jt.extend({
  method: me("notifications/resources/list_changed")
}), fc = ft.extend({
  method: me("resources/subscribe"),
  params: Pt.extend({
    /**
     * The URI of the resource to subscribe to. The URI can use any protocol; it is up to the server how to interpret it.
     */
    uri: H()
  })
}), pc = ft.extend({
  method: me("resources/unsubscribe"),
  params: Pt.extend({
    /**
     * The URI of the resource to unsubscribe from.
     */
    uri: H()
  })
}), mc = jt.extend({
  method: me("notifications/resources/updated"),
  params: Dr.extend({
    /**
     * The URI of the resource that has been updated. This might be a sub-resource of the one that the client actually subscribed to.
     */
    uri: H()
  })
}), vc = W({
  /**
   * The name of the argument.
   */
  name: H(),
  /**
   * A human-readable description of the argument.
   */
  description: q(H()),
  /**
   * Whether this argument must be provided.
   */
  required: q(ct())
}).passthrough(), gc = jr.extend({
  /**
   * An optional description of what this prompt provides
   */
  description: q(H()),
  /**
   * A list of arguments to use for templating the prompt.
   */
  arguments: q(Ke(vc)),
  /**
   * See [MCP specification](https://github.com/modelcontextprotocol/modelcontextprotocol/blob/47339c03c143bb4ec01a26e721a1b8fe66634ebe/docs/specification/draft/basic/index.mdx#general-fields)
   * for notes on _meta usage.
   */
  _meta: q(W({}).passthrough())
}), Ns = pa.extend({
  method: me("prompts/list")
}), yc = ma.extend({
  prompts: Ke(gc)
}), Ds = ft.extend({
  method: me("prompts/get"),
  params: Pt.extend({
    /**
     * The name of the prompt or prompt template.
     */
    name: H(),
    /**
     * Arguments to use for templating the prompt.
     */
    arguments: q(gr(H()))
  })
}), tn = W({
  type: me("text"),
  /**
   * The text content of the message.
   */
  text: H(),
  /**
   * See [MCP specification](https://github.com/modelcontextprotocol/modelcontextprotocol/blob/47339c03c143bb4ec01a26e721a1b8fe66634ebe/docs/specification/draft/basic/index.mdx#general-fields)
   * for notes on _meta usage.
   */
  _meta: q(W({}).passthrough())
}).passthrough(), rn = W({
  type: me("image"),
  /**
   * The base64-encoded image data.
   */
  data: en,
  /**
   * The MIME type of the image. Different providers may support different image types.
   */
  mimeType: H(),
  /**
   * See [MCP specification](https://github.com/modelcontextprotocol/modelcontextprotocol/blob/47339c03c143bb4ec01a26e721a1b8fe66634ebe/docs/specification/draft/basic/index.mdx#general-fields)
   * for notes on _meta usage.
   */
  _meta: q(W({}).passthrough())
}).passthrough(), an = W({
  type: me("audio"),
  /**
   * The base64-encoded audio data.
   */
  data: en,
  /**
   * The MIME type of the audio. Different providers may support different audio types.
   */
  mimeType: H(),
  /**
   * See [MCP specification](https://github.com/modelcontextprotocol/modelcontextprotocol/blob/47339c03c143bb4ec01a26e721a1b8fe66634ebe/docs/specification/draft/basic/index.mdx#general-fields)
   * for notes on _meta usage.
   */
  _meta: q(W({}).passthrough())
}).passthrough(), _c = W({
  type: me("resource"),
  resource: ut([_o, bo]),
  /**
   * See [MCP specification](https://github.com/modelcontextprotocol/modelcontextprotocol/blob/47339c03c143bb4ec01a26e721a1b8fe66634ebe/docs/specification/draft/basic/index.mdx#general-fields)
   * for notes on _meta usage.
   */
  _meta: q(W({}).passthrough())
}).passthrough(), bc = Po.extend({
  type: me("resource_link")
}), So = ut([
  tn,
  rn,
  an,
  bc,
  _c
]), Pc = W({
  role: Nt(["user", "assistant"]),
  content: So
}).passthrough(), Sc = St.extend({
  /**
   * An optional description for the prompt.
   */
  description: q(H()),
  messages: Ke(Pc)
}), wc = jt.extend({
  method: me("notifications/prompts/list_changed")
}), xc = W({
  /**
   * A human-readable title for the tool.
   */
  title: q(H()),
  /**
   * If true, the tool does not modify its environment.
   *
   * Default: false
   */
  readOnlyHint: q(ct()),
  /**
   * If true, the tool may perform destructive updates to its environment.
   * If false, the tool performs only additive updates.
   *
   * (This property is meaningful only when `readOnlyHint == false`)
   *
   * Default: true
   */
  destructiveHint: q(ct()),
  /**
   * If true, calling the tool repeatedly with the same arguments
   * will have no additional effect on the its environment.
   *
   * (This property is meaningful only when `readOnlyHint == false`)
   *
   * Default: false
   */
  idempotentHint: q(ct()),
  /**
   * If true, this tool may interact with an "open world" of external
   * entities. If false, the tool's domain of interaction is closed.
   * For example, the world of a web search tool is open, whereas that
   * of a memory tool is not.
   *
   * Default: true
   */
  openWorldHint: q(ct())
}).passthrough(), Ec = jr.extend({
  /**
   * A human-readable description of the tool.
   */
  description: q(H()),
  /**
   * A JSON Schema object defining the expected parameters for the tool.
   */
  inputSchema: W({
    type: me("object"),
    properties: q(W({}).passthrough()),
    required: q(Ke(H()))
  }).passthrough(),
  /**
   * An optional JSON Schema object defining the structure of the tool's output returned in
   * the structuredContent field of a CallToolResult.
   */
  outputSchema: q(W({
    type: me("object"),
    properties: q(W({}).passthrough()),
    required: q(Ke(H()))
  }).passthrough()),
  /**
   * Optional additional tool information.
   */
  annotations: q(xc),
  /**
   * See [MCP specification](https://github.com/modelcontextprotocol/modelcontextprotocol/blob/47339c03c143bb4ec01a26e721a1b8fe66634ebe/docs/specification/draft/basic/index.mdx#general-fields)
   * for notes on _meta usage.
   */
  _meta: q(W({}).passthrough())
}), js = pa.extend({
  method: me("tools/list")
}), Rc = ma.extend({
  tools: Ke(Ec)
}), wo = St.extend({
  /**
   * A list of content objects that represent the result of the tool call.
   *
   * If the Tool does not define an outputSchema, this field MUST be present in the result.
   * For backwards compatibility, this field is always present, but it may be empty.
   */
  content: Ke(So).default([]),
  /**
   * An object containing structured tool output.
   *
   * If the Tool defines an outputSchema, this field MUST be present in the result, and contain a JSON object that matches the schema.
   */
  structuredContent: W({}).passthrough().optional(),
  /**
   * Whether the tool call ended in an error.
   *
   * If not set, this is assumed to be false (the call was successful).
   *
   * Any errors that originate from the tool SHOULD be reported inside the result
   * object, with `isError` set to true, _not_ as an MCP protocol-level error
   * response. Otherwise, the LLM would not be able to see that an error occurred
   * and self-correct.
   *
   * However, any errors in _finding_ the tool, an error indicating that the
   * server does not support tool calls, or any other exceptional conditions,
   * should be reported as an MCP error response.
   */
  isError: q(ct())
});
wo.or(St.extend({
  toolResult: Nr()
}));
const Fs = ft.extend({
  method: me("tools/call"),
  params: Pt.extend({
    name: H(),
    arguments: q(gr(Nr()))
  })
}), kc = jt.extend({
  method: me("notifications/tools/list_changed")
}), xo = Nt([
  "debug",
  "info",
  "notice",
  "warning",
  "error",
  "critical",
  "alert",
  "emergency"
]), Tc = ft.extend({
  method: me("logging/setLevel"),
  params: Pt.extend({
    /**
     * The level of logging that the client wants to receive from the server. The server should send all logs at this level and higher (i.e., more severe) to the client as notifications/logging/message.
     */
    level: xo
  })
}), Ac = jt.extend({
  method: me("notifications/message"),
  params: Dr.extend({
    /**
     * The severity of this log message.
     */
    level: xo,
    /**
     * An optional name of the logger issuing this message.
     */
    logger: q(H()),
    /**
     * The data to be logged, such as a string message or an object. Any JSON serializable type is allowed here.
     */
    data: Nr()
  })
}), Oc = W({
  /**
   * A hint for a model name.
   */
  name: H().optional()
}).passthrough(), Cc = W({
  /**
   * Optional hints to use for model selection.
   */
  hints: q(Ke(Oc)),
  /**
   * How much to prioritize cost when selecting a model.
   */
  costPriority: q(et().min(0).max(1)),
  /**
   * How much to prioritize sampling speed (latency) when selecting a model.
   */
  speedPriority: q(et().min(0).max(1)),
  /**
   * How much to prioritize intelligence and capabilities when selecting a model.
   */
  intelligencePriority: q(et().min(0).max(1))
}).passthrough(), $c = W({
  role: Nt(["user", "assistant"]),
  content: ut([tn, rn, an])
}).passthrough(), Ic = ft.extend({
  method: me("sampling/createMessage"),
  params: Pt.extend({
    messages: Ke($c),
    /**
     * An optional system prompt the server wants to use for sampling. The client MAY modify or omit this prompt.
     */
    systemPrompt: q(H()),
    /**
     * A request to include context from one or more MCP servers (including the caller), to be attached to the prompt. The client MAY ignore this request.
     */
    includeContext: q(Nt(["none", "thisServer", "allServers"])),
    temperature: q(et()),
    /**
     * The maximum number of tokens to sample, as requested by the server. The client MAY choose to sample fewer tokens than requested.
     */
    maxTokens: et().int(),
    stopSequences: q(Ke(H())),
    /**
     * Optional metadata to pass through to the LLM provider. The format of this metadata is provider-specific.
     */
    metadata: q(W({}).passthrough()),
    /**
     * The server's preferences for which model to select.
     */
    modelPreferences: q(Cc)
  })
}), Eo = St.extend({
  /**
   * The name of the model that generated the message.
   */
  model: H(),
  /**
   * The reason why sampling stopped.
   */
  stopReason: q(Nt(["endTurn", "stopSequence", "maxTokens"]).or(H())),
  role: Nt(["user", "assistant"]),
  content: Yl("type", [
    tn,
    rn,
    an
  ])
}), Nc = W({
  type: me("boolean"),
  title: q(H()),
  description: q(H()),
  default: q(ct())
}).passthrough(), Dc = W({
  type: me("string"),
  title: q(H()),
  description: q(H()),
  minLength: q(et()),
  maxLength: q(et()),
  format: q(Nt(["email", "uri", "date", "date-time"]))
}).passthrough(), jc = W({
  type: Nt(["number", "integer"]),
  title: q(H()),
  description: q(H()),
  minimum: q(et()),
  maximum: q(et())
}).passthrough(), Fc = W({
  type: me("string"),
  title: q(H()),
  description: q(H()),
  enum: Ke(H()),
  enumNames: q(Ke(H()))
}).passthrough(), qc = ut([
  Nc,
  Dc,
  jc,
  Fc
]), Lc = ft.extend({
  method: me("elicitation/create"),
  params: Pt.extend({
    /**
     * The message to present to the user.
     */
    message: H(),
    /**
     * The schema for the requested user input.
     */
    requestedSchema: W({
      type: me("object"),
      properties: gr(H(), qc),
      required: q(Ke(H()))
    }).passthrough()
  })
}), Ro = St.extend({
  /**
   * The user's response action.
   */
  action: Nt(["accept", "decline", "cancel"]),
  /**
   * The collected user input content (only present if action is "accept").
   */
  content: q(gr(H(), Nr()))
}), Zc = W({
  type: me("ref/resource"),
  /**
   * The URI or URI template of the resource.
   */
  uri: H()
}).passthrough(), Mc = W({
  type: me("ref/prompt"),
  /**
   * The name of the prompt or prompt template
   */
  name: H()
}).passthrough(), qs = ft.extend({
  method: me("completion/complete"),
  params: Pt.extend({
    ref: ut([Mc, Zc]),
    /**
     * The argument's information
     */
    argument: W({
      /**
       * The name of the argument
       */
      name: H(),
      /**
       * The value of the argument to use for completion matching.
       */
      value: H()
    }).passthrough(),
    context: q(W({
      /**
       * Previously-resolved variables in a URI template or prompt.
       */
      arguments: q(gr(H(), H()))
    }))
  })
}), zc = St.extend({
  completion: W({
    /**
     * An array of completion values. Must not exceed 100 items.
     */
    values: Ke(H()).max(100),
    /**
     * The total number of completion options available. This can exceed the number of values actually sent in the response.
     */
    total: q(et().int()),
    /**
     * Indicates whether there are additional completion options beyond those provided in the current response, even if the exact total is unknown.
     */
    hasMore: q(ct())
  }).passthrough()
}), Uc = W({
  /**
   * The URI identifying the root. This *must* start with file:// for now.
   */
  uri: H().startsWith("file://"),
  /**
   * An optional name for the root.
   */
  name: q(H()),
  /**
   * See [MCP specification](https://github.com/modelcontextprotocol/modelcontextprotocol/blob/47339c03c143bb4ec01a26e721a1b8fe66634ebe/docs/specification/draft/basic/index.mdx#general-fields)
   * for notes on _meta usage.
   */
  _meta: q(W({}).passthrough())
}).passthrough(), Vc = ft.extend({
  method: me("roots/list")
}), ko = St.extend({
  roots: Ke(Uc)
}), Hc = jt.extend({
  method: me("notifications/roots/list_changed")
});
ut([
  Ys,
  vo,
  qs,
  Tc,
  Ds,
  Ns,
  Cs,
  $s,
  Is,
  fc,
  pc,
  Fs,
  js
]);
ut([
  Gs,
  Xs,
  go,
  Hc
]);
ut([
  Ws,
  Eo,
  Ro,
  ko
]);
ut([
  Ys,
  Ic,
  Lc,
  Vc
]);
ut([
  Gs,
  Xs,
  Ac,
  mc,
  hc,
  kc,
  wc
]);
ut([
  Ws,
  ic,
  zc,
  Sc,
  yc,
  lc,
  cc,
  dc,
  wo,
  Rc
]);
class Ve extends Error {
  constructor(e, r, a) {
    super(`MCP error ${e}: ${r}`), this.code = e, this.data = a, this.name = "McpError";
  }
}
const Bc = 6e4;
class Qc {
  constructor(e) {
    this._options = e, this._requestMessageId = 0, this._requestHandlers = /* @__PURE__ */ new Map(), this._requestHandlerAbortControllers = /* @__PURE__ */ new Map(), this._notificationHandlers = /* @__PURE__ */ new Map(), this._responseHandlers = /* @__PURE__ */ new Map(), this._progressHandlers = /* @__PURE__ */ new Map(), this._timeoutInfo = /* @__PURE__ */ new Map(), this._pendingDebouncedNotifications = /* @__PURE__ */ new Set(), this.setNotificationHandler(Gs, (r) => {
      const a = this._requestHandlerAbortControllers.get(r.params.requestId);
      a == null || a.abort(r.params.reason);
    }), this.setNotificationHandler(Xs, (r) => {
      this._onprogress(r);
    }), this.setRequestHandler(
      Ys,
      // Automatic pong by default.
      (r) => ({})
    );
  }
  _setupTimeout(e, r, a, t, n = !1) {
    this._timeoutInfo.set(e, {
      timeoutId: setTimeout(t, r),
      startTime: Date.now(),
      timeout: r,
      maxTotalTimeout: a,
      resetTimeoutOnProgress: n,
      onTimeout: t
    });
  }
  _resetTimeout(e) {
    const r = this._timeoutInfo.get(e);
    if (!r)
      return !1;
    const a = Date.now() - r.startTime;
    if (r.maxTotalTimeout && a >= r.maxTotalTimeout)
      throw this._timeoutInfo.delete(e), new Ve(qe.RequestTimeout, "Maximum total timeout exceeded", { maxTotalTimeout: r.maxTotalTimeout, totalElapsed: a });
    return clearTimeout(r.timeoutId), r.timeoutId = setTimeout(r.onTimeout, r.timeout), !0;
  }
  _cleanupTimeout(e) {
    const r = this._timeoutInfo.get(e);
    r && (clearTimeout(r.timeoutId), this._timeoutInfo.delete(e));
  }
  /**
   * Attaches to the given transport, starts it, and starts listening for messages.
   *
   * The Protocol object assumes ownership of the Transport, replacing any callbacks that have already been set, and expects that it is the only user of the Transport instance going forward.
   */
  async connect(e) {
    var r, a, t;
    this._transport = e;
    const n = (r = this.transport) === null || r === void 0 ? void 0 : r.onclose;
    this._transport.onclose = () => {
      n == null || n(), this._onclose();
    };
    const i = (a = this.transport) === null || a === void 0 ? void 0 : a.onerror;
    this._transport.onerror = (u) => {
      i == null || i(u), this._onerror(u);
    };
    const o = (t = this._transport) === null || t === void 0 ? void 0 : t.onmessage;
    this._transport.onmessage = (u, l) => {
      o == null || o(u, l), zn(u) || ac(u) ? this._onresponse(u) : tc(u) ? this._onrequest(u, l) : rc(u) ? this._onnotification(u) : this._onerror(new Error(`Unknown message type: ${JSON.stringify(u)}`));
    }, await this._transport.start();
  }
  _onclose() {
    var e;
    const r = this._responseHandlers;
    this._responseHandlers = /* @__PURE__ */ new Map(), this._progressHandlers.clear(), this._pendingDebouncedNotifications.clear(), this._transport = void 0, (e = this.onclose) === null || e === void 0 || e.call(this);
    const a = new Ve(qe.ConnectionClosed, "Connection closed");
    for (const t of r.values())
      t(a);
  }
  _onerror(e) {
    var r;
    (r = this.onerror) === null || r === void 0 || r.call(this, e);
  }
  _onnotification(e) {
    var r;
    const a = (r = this._notificationHandlers.get(e.method)) !== null && r !== void 0 ? r : this.fallbackNotificationHandler;
    a !== void 0 && Promise.resolve().then(() => a(e)).catch((t) => this._onerror(new Error(`Uncaught error in notification handler: ${t}`)));
  }
  _onrequest(e, r) {
    var a, t;
    const n = (a = this._requestHandlers.get(e.method)) !== null && a !== void 0 ? a : this.fallbackRequestHandler, i = this._transport;
    if (n === void 0) {
      i == null || i.send({
        jsonrpc: "2.0",
        id: e.id,
        error: {
          code: qe.MethodNotFound,
          message: "Method not found"
        }
      }).catch((l) => this._onerror(new Error(`Failed to send an error response: ${l}`)));
      return;
    }
    const o = new AbortController();
    this._requestHandlerAbortControllers.set(e.id, o);
    const u = {
      signal: o.signal,
      sessionId: i == null ? void 0 : i.sessionId,
      _meta: (t = e.params) === null || t === void 0 ? void 0 : t._meta,
      sendNotification: (l) => this.notification(l, { relatedRequestId: e.id }),
      sendRequest: (l, h, m) => this.request(l, h, { ...m, relatedRequestId: e.id }),
      authInfo: r == null ? void 0 : r.authInfo,
      requestId: e.id,
      requestInfo: r == null ? void 0 : r.requestInfo
    };
    Promise.resolve().then(() => n(e, u)).then((l) => {
      if (!o.signal.aborted)
        return i == null ? void 0 : i.send({
          result: l,
          jsonrpc: "2.0",
          id: e.id
        });
    }, (l) => {
      var h;
      if (!o.signal.aborted)
        return i == null ? void 0 : i.send({
          jsonrpc: "2.0",
          id: e.id,
          error: {
            code: Number.isSafeInteger(l.code) ? l.code : qe.InternalError,
            message: (h = l.message) !== null && h !== void 0 ? h : "Internal error"
          }
        });
    }).catch((l) => this._onerror(new Error(`Failed to send response: ${l}`))).finally(() => {
      this._requestHandlerAbortControllers.delete(e.id);
    });
  }
  _onprogress(e) {
    const { progressToken: r, ...a } = e.params, t = Number(r), n = this._progressHandlers.get(t);
    if (!n) {
      this._onerror(new Error(`Received a progress notification for an unknown token: ${JSON.stringify(e)}`));
      return;
    }
    const i = this._responseHandlers.get(t), o = this._timeoutInfo.get(t);
    if (o && i && o.resetTimeoutOnProgress)
      try {
        this._resetTimeout(t);
      } catch (u) {
        i(u);
        return;
      }
    n(a);
  }
  _onresponse(e) {
    const r = Number(e.id), a = this._responseHandlers.get(r);
    if (a === void 0) {
      this._onerror(new Error(`Received a response for an unknown message ID: ${JSON.stringify(e)}`));
      return;
    }
    if (this._responseHandlers.delete(r), this._progressHandlers.delete(r), this._cleanupTimeout(r), zn(e))
      a(e);
    else {
      const t = new Ve(e.error.code, e.error.message, e.error.data);
      a(t);
    }
  }
  get transport() {
    return this._transport;
  }
  /**
   * Closes the connection.
   */
  async close() {
    var e;
    await ((e = this._transport) === null || e === void 0 ? void 0 : e.close());
  }
  /**
   * Sends a request and wait for a response.
   *
   * Do not use this method to emit notifications! Use notification() instead.
   */
  request(e, r, a) {
    const { relatedRequestId: t, resumptionToken: n, onresumptiontoken: i } = a ?? {};
    return new Promise((o, u) => {
      var l, h, m, _, c, f;
      if (!this._transport) {
        u(new Error("Not connected"));
        return;
      }
      ((l = this._options) === null || l === void 0 ? void 0 : l.enforceStrictCapabilities) === !0 && this.assertCapabilityForMethod(e.method), (h = a == null ? void 0 : a.signal) === null || h === void 0 || h.throwIfAborted();
      const y = this._requestMessageId++, v = {
        ...e,
        jsonrpc: "2.0",
        id: y
      };
      a != null && a.onprogress && (this._progressHandlers.set(y, a.onprogress), v.params = {
        ...e.params,
        _meta: {
          ...((m = e.params) === null || m === void 0 ? void 0 : m._meta) || {},
          progressToken: y
        }
      });
      const P = (b) => {
        var x;
        this._responseHandlers.delete(y), this._progressHandlers.delete(y), this._cleanupTimeout(y), (x = this._transport) === null || x === void 0 || x.send({
          jsonrpc: "2.0",
          method: "notifications/cancelled",
          params: {
            requestId: y,
            reason: String(b)
          }
        }, { relatedRequestId: t, resumptionToken: n, onresumptiontoken: i }).catch((T) => this._onerror(new Error(`Failed to send cancellation: ${T}`))), u(b);
      };
      this._responseHandlers.set(y, (b) => {
        var x;
        if (!(!((x = a == null ? void 0 : a.signal) === null || x === void 0) && x.aborted)) {
          if (b instanceof Error)
            return u(b);
          try {
            const T = r.parse(b.result);
            o(T);
          } catch (T) {
            u(T);
          }
        }
      }), (_ = a == null ? void 0 : a.signal) === null || _ === void 0 || _.addEventListener("abort", () => {
        var b;
        P((b = a == null ? void 0 : a.signal) === null || b === void 0 ? void 0 : b.reason);
      });
      const I = (c = a == null ? void 0 : a.timeout) !== null && c !== void 0 ? c : Bc, A = () => P(new Ve(qe.RequestTimeout, "Request timed out", { timeout: I }));
      this._setupTimeout(y, I, a == null ? void 0 : a.maxTotalTimeout, A, (f = a == null ? void 0 : a.resetTimeoutOnProgress) !== null && f !== void 0 ? f : !1), this._transport.send(v, { relatedRequestId: t, resumptionToken: n, onresumptiontoken: i }).catch((b) => {
        this._cleanupTimeout(y), u(b);
      });
    });
  }
  /**
   * Emits a notification, which is a one-way message that does not expect a response.
   */
  async notification(e, r) {
    var a, t;
    if (!this._transport)
      throw new Error("Not connected");
    if (this.assertNotificationCapability(e.method), ((t = (a = this._options) === null || a === void 0 ? void 0 : a.debouncedNotificationMethods) !== null && t !== void 0 ? t : []).includes(e.method) && !e.params && !(r != null && r.relatedRequestId)) {
      if (this._pendingDebouncedNotifications.has(e.method))
        return;
      this._pendingDebouncedNotifications.add(e.method), Promise.resolve().then(() => {
        var u;
        if (this._pendingDebouncedNotifications.delete(e.method), !this._transport)
          return;
        const l = {
          ...e,
          jsonrpc: "2.0"
        };
        (u = this._transport) === null || u === void 0 || u.send(l, r).catch((h) => this._onerror(h));
      });
      return;
    }
    const o = {
      ...e,
      jsonrpc: "2.0"
    };
    await this._transport.send(o, r);
  }
  /**
   * Registers a handler to invoke when this protocol object receives a request with the given method.
   *
   * Note that this will replace any previous request handler for the same method.
   */
  setRequestHandler(e, r) {
    const a = e.shape.method.value;
    this.assertRequestHandlerCapability(a), this._requestHandlers.set(a, (t, n) => Promise.resolve(r(e.parse(t), n)));
  }
  /**
   * Removes the request handler for the given method.
   */
  removeRequestHandler(e) {
    this._requestHandlers.delete(e);
  }
  /**
   * Asserts that a request handler has not already been set for the given method, in preparation for a new one being automatically installed.
   */
  assertCanSetRequestHandler(e) {
    if (this._requestHandlers.has(e))
      throw new Error(`A request handler for ${e} already exists, which would be overridden`);
  }
  /**
   * Registers a handler to invoke when this protocol object receives a notification with the given method.
   *
   * Note that this will replace any previous notification handler for the same method.
   */
  setNotificationHandler(e, r) {
    this._notificationHandlers.set(e.shape.method.value, (a) => Promise.resolve(r(e.parse(a))));
  }
  /**
   * Removes the notification handler for the given method.
   */
  removeNotificationHandler(e) {
    this._notificationHandlers.delete(e);
  }
}
function Kc(s, e) {
  return Object.entries(e).reduce((r, [a, t]) => (t && typeof t == "object" ? r[a] = r[a] ? { ...r[a], ...t } : t : r[a] = t, r), { ...s });
}
function Jc(s) {
  return s && s.__esModule && Object.prototype.hasOwnProperty.call(s, "default") ? s.default : s;
}
var xr = { exports: {} };
/** @license URI.js v4.4.1 (c) 2011 Gary Court. License: http://github.com/garycourt/uri-js */
var Wc = xr.exports, Un;
function Gc() {
  return Un || (Un = 1, function(s, e) {
    (function(r, a) {
      a(e);
    })(Wc, function(r) {
      function a() {
        for (var p = arguments.length, d = Array(p), g = 0; g < p; g++)
          d[g] = arguments[g];
        if (d.length > 1) {
          d[0] = d[0].slice(0, -1);
          for (var k = d.length - 1, R = 1; R < k; ++R)
            d[R] = d[R].slice(1, -1);
          return d[k] = d[k].slice(1), d.join("");
        } else
          return d[0];
      }
      function t(p) {
        return "(?:" + p + ")";
      }
      function n(p) {
        return p === void 0 ? "undefined" : p === null ? "null" : Object.prototype.toString.call(p).split(" ").pop().split("]").shift().toLowerCase();
      }
      function i(p) {
        return p.toUpperCase();
      }
      function o(p) {
        return p != null ? p instanceof Array ? p : typeof p.length != "number" || p.split || p.setInterval || p.call ? [p] : Array.prototype.slice.call(p) : [];
      }
      function u(p, d) {
        var g = p;
        if (d)
          for (var k in d)
            g[k] = d[k];
        return g;
      }
      function l(p) {
        var d = "[A-Za-z]", g = "[0-9]", k = a(g, "[A-Fa-f]"), R = t(t("%[EFef]" + k + "%" + k + k + "%" + k + k) + "|" + t("%[89A-Fa-f]" + k + "%" + k + k) + "|" + t("%" + k + k)), ae = "[\\:\\/\\?\\#\\[\\]\\@]", se = "[\\!\\$\\&\\'\\(\\)\\*\\+\\,\\;\\=]", Ee = a(ae, se), Ie = p ? "[\\xA0-\\u200D\\u2010-\\u2029\\u202F-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF]" : "[]", ze = p ? "[\\uE000-\\uF8FF]" : "[]", we = a(d, g, "[\\-\\.\\_\\~]", Ie);
        t(d + a(d, g, "[\\+\\-\\.]") + "*"), t(t(R + "|" + a(we, se, "[\\:]")) + "*");
        var $e = t(t("25[0-5]") + "|" + t("2[0-4]" + g) + "|" + t("1" + g + g) + "|" + t("0?[1-9]" + g) + "|0?0?" + g), Ue = t($e + "\\." + $e + "\\." + $e + "\\." + $e), he = t(k + "{1,4}"), De = t(t(he + "\\:" + he) + "|" + Ue), He = t(t(he + "\\:") + "{6}" + De), je = t("\\:\\:" + t(he + "\\:") + "{5}" + De), Qt = t(t(he) + "?\\:\\:" + t(he + "\\:") + "{4}" + De), kt = t(t(t(he + "\\:") + "{0,1}" + he) + "?\\:\\:" + t(he + "\\:") + "{3}" + De), Tt = t(t(t(he + "\\:") + "{0,2}" + he) + "?\\:\\:" + t(he + "\\:") + "{2}" + De), cr = t(t(t(he + "\\:") + "{0,3}" + he) + "?\\:\\:" + he + "\\:" + De), Xt = t(t(t(he + "\\:") + "{0,4}" + he) + "?\\:\\:" + De), vt = t(t(t(he + "\\:") + "{0,5}" + he) + "?\\:\\:" + he), At = t(t(t(he + "\\:") + "{0,6}" + he) + "?\\:\\:"), er = t([He, je, Qt, kt, Tt, cr, Xt, vt, At].join("|")), qt = t(t(we + "|" + R) + "+");
        t("[vV]" + k + "+\\." + a(we, se, "[\\:]") + "+"), t(t(R + "|" + a(we, se)) + "*");
        var Pr = t(R + "|" + a(we, se, "[\\:\\@]"));
        return t(t(R + "|" + a(we, se, "[\\@]")) + "+"), t(t(Pr + "|" + a("[\\/\\?]", ze)) + "*"), {
          NOT_SCHEME: new RegExp(a("[^]", d, g, "[\\+\\-\\.]"), "g"),
          NOT_USERINFO: new RegExp(a("[^\\%\\:]", we, se), "g"),
          NOT_HOST: new RegExp(a("[^\\%\\[\\]\\:]", we, se), "g"),
          NOT_PATH: new RegExp(a("[^\\%\\/\\:\\@]", we, se), "g"),
          NOT_PATH_NOSCHEME: new RegExp(a("[^\\%\\/\\@]", we, se), "g"),
          NOT_QUERY: new RegExp(a("[^\\%]", we, se, "[\\:\\@\\/\\?]", ze), "g"),
          NOT_FRAGMENT: new RegExp(a("[^\\%]", we, se, "[\\:\\@\\/\\?]"), "g"),
          ESCAPE: new RegExp(a("[^]", we, se), "g"),
          UNRESERVED: new RegExp(we, "g"),
          OTHER_CHARS: new RegExp(a("[^\\%]", we, Ee), "g"),
          PCT_ENCODED: new RegExp(R, "g"),
          IPV4ADDRESS: new RegExp("^(" + Ue + ")$"),
          IPV6ADDRESS: new RegExp("^\\[?(" + er + ")" + t(t("\\%25|\\%(?!" + k + "{2})") + "(" + qt + ")") + "?\\]?$")
          //RFC 6874, with relaxed parsing rules
        };
      }
      var h = l(!1), m = l(!0), _ = /* @__PURE__ */ function() {
        function p(d, g) {
          var k = [], R = !0, ae = !1, se = void 0;
          try {
            for (var Ee = d[Symbol.iterator](), Ie; !(R = (Ie = Ee.next()).done) && (k.push(Ie.value), !(g && k.length === g)); R = !0)
              ;
          } catch (ze) {
            ae = !0, se = ze;
          } finally {
            try {
              !R && Ee.return && Ee.return();
            } finally {
              if (ae) throw se;
            }
          }
          return k;
        }
        return function(d, g) {
          if (Array.isArray(d))
            return d;
          if (Symbol.iterator in Object(d))
            return p(d, g);
          throw new TypeError("Invalid attempt to destructure non-iterable instance");
        };
      }(), c = function(p) {
        if (Array.isArray(p)) {
          for (var d = 0, g = Array(p.length); d < p.length; d++) g[d] = p[d];
          return g;
        } else
          return Array.from(p);
      }, f = 2147483647, y = 36, v = 1, P = 26, I = 38, A = 700, b = 72, x = 128, T = "-", O = /^xn--/, C = /[^\0-\x7E]/, $ = /[\x2E\u3002\uFF0E\uFF61]/g, S = {
        overflow: "Overflow: input needs wider integers to process",
        "not-basic": "Illegal input >= 0x80 (not a basic code point)",
        "invalid-input": "Invalid input"
      }, E = y - v, N = Math.floor, L = String.fromCharCode;
      function Z(p) {
        throw new RangeError(S[p]);
      }
      function re(p, d) {
        for (var g = [], k = p.length; k--; )
          g[k] = d(p[k]);
        return g;
      }
      function fe(p, d) {
        var g = p.split("@"), k = "";
        g.length > 1 && (k = g[0] + "@", p = g[1]), p = p.replace($, ".");
        var R = p.split("."), ae = re(R, d).join(".");
        return k + ae;
      }
      function ne(p) {
        for (var d = [], g = 0, k = p.length; g < k; ) {
          var R = p.charCodeAt(g++);
          if (R >= 55296 && R <= 56319 && g < k) {
            var ae = p.charCodeAt(g++);
            (ae & 64512) == 56320 ? d.push(((R & 1023) << 10) + (ae & 1023) + 65536) : (d.push(R), g--);
          } else
            d.push(R);
        }
        return d;
      }
      var _e = function(d) {
        return String.fromCodePoint.apply(String, c(d));
      }, ce = function(d) {
        return d - 48 < 10 ? d - 22 : d - 65 < 26 ? d - 65 : d - 97 < 26 ? d - 97 : y;
      }, de = function(d, g) {
        return d + 22 + 75 * (d < 26) - ((g != 0) << 5);
      }, tt = function(d, g, k) {
        var R = 0;
        for (
          d = k ? N(d / A) : d >> 1, d += N(d / g);
          /* no initialization */
          d > E * P >> 1;
          R += y
        )
          d = N(d / E);
        return N(R + (E + 1) * d / (d + I));
      }, Ge = function(d) {
        var g = [], k = d.length, R = 0, ae = x, se = b, Ee = d.lastIndexOf(T);
        Ee < 0 && (Ee = 0);
        for (var Ie = 0; Ie < Ee; ++Ie)
          d.charCodeAt(Ie) >= 128 && Z("not-basic"), g.push(d.charCodeAt(Ie));
        for (var ze = Ee > 0 ? Ee + 1 : 0; ze < k; ) {
          for (
            var we = R, $e = 1, Ue = y;
            ;
            /* no condition */
            Ue += y
          ) {
            ze >= k && Z("invalid-input");
            var he = ce(d.charCodeAt(ze++));
            (he >= y || he > N((f - R) / $e)) && Z("overflow"), R += he * $e;
            var De = Ue <= se ? v : Ue >= se + P ? P : Ue - se;
            if (he < De)
              break;
            var He = y - De;
            $e > N(f / He) && Z("overflow"), $e *= He;
          }
          var je = g.length + 1;
          se = tt(R - we, je, we == 0), N(R / je) > f - ae && Z("overflow"), ae += N(R / je), R %= je, g.splice(R++, 0, ae);
        }
        return String.fromCodePoint.apply(String, g);
      }, Je = function(d) {
        var g = [];
        d = ne(d);
        var k = d.length, R = x, ae = 0, se = b, Ee = !0, Ie = !1, ze = void 0;
        try {
          for (var we = d[Symbol.iterator](), $e; !(Ee = ($e = we.next()).done); Ee = !0) {
            var Ue = $e.value;
            Ue < 128 && g.push(L(Ue));
          }
        } catch (Sr) {
          Ie = !0, ze = Sr;
        } finally {
          try {
            !Ee && we.return && we.return();
          } finally {
            if (Ie)
              throw ze;
          }
        }
        var he = g.length, De = he;
        for (he && g.push(T); De < k; ) {
          var He = f, je = !0, Qt = !1, kt = void 0;
          try {
            for (var Tt = d[Symbol.iterator](), cr; !(je = (cr = Tt.next()).done); je = !0) {
              var Xt = cr.value;
              Xt >= R && Xt < He && (He = Xt);
            }
          } catch (Sr) {
            Qt = !0, kt = Sr;
          } finally {
            try {
              !je && Tt.return && Tt.return();
            } finally {
              if (Qt)
                throw kt;
            }
          }
          var vt = De + 1;
          He - R > N((f - ae) / vt) && Z("overflow"), ae += (He - R) * vt, R = He;
          var At = !0, er = !1, qt = void 0;
          try {
            for (var Pr = d[Symbol.iterator](), vn; !(At = (vn = Pr.next()).done); At = !0) {
              var gn = vn.value;
              if (gn < R && ++ae > f && Z("overflow"), gn == R) {
                for (
                  var qr = ae, Lr = y;
                  ;
                  /* no condition */
                  Lr += y
                ) {
                  var Zr = Lr <= se ? v : Lr >= se + P ? P : Lr - se;
                  if (qr < Zr)
                    break;
                  var yn = qr - Zr, _n = y - Zr;
                  g.push(L(de(Zr + yn % _n, 0))), qr = N(yn / _n);
                }
                g.push(L(de(qr, 0))), se = tt(ae, vt, De == he), ae = 0, ++De;
              }
            }
          } catch (Sr) {
            er = !0, qt = Sr;
          } finally {
            try {
              !At && Pr.return && Pr.return();
            } finally {
              if (er)
                throw qt;
            }
          }
          ++ae, ++R;
        }
        return g.join("");
      }, Ze = function(d) {
        return fe(d, function(g) {
          return O.test(g) ? Ge(g.slice(4).toLowerCase()) : g;
        });
      }, mt = function(d) {
        return fe(d, function(g) {
          return C.test(g) ? "xn--" + Je(g) : g;
        });
      }, w = {
        /**
         * A string representing the current Punycode.js version number.
         * @memberOf punycode
         * @type String
         */
        version: "2.1.0",
        /**
         * An object of methods to convert from JavaScript's internal character
         * representation (UCS-2) to Unicode code points, and back.
         * @see <https://mathiasbynens.be/notes/javascript-encoding>
         * @memberOf punycode
         * @type Object
         */
        ucs2: {
          decode: ne,
          encode: _e
        },
        decode: Ge,
        encode: Je,
        toASCII: mt,
        toUnicode: Ze
      }, D = {};
      function B(p) {
        var d = p.charCodeAt(0), g = void 0;
        return d < 16 ? g = "%0" + d.toString(16).toUpperCase() : d < 128 ? g = "%" + d.toString(16).toUpperCase() : d < 2048 ? g = "%" + (d >> 6 | 192).toString(16).toUpperCase() + "%" + (d & 63 | 128).toString(16).toUpperCase() : g = "%" + (d >> 12 | 224).toString(16).toUpperCase() + "%" + (d >> 6 & 63 | 128).toString(16).toUpperCase() + "%" + (d & 63 | 128).toString(16).toUpperCase(), g;
      }
      function le(p) {
        for (var d = "", g = 0, k = p.length; g < k; ) {
          var R = parseInt(p.substr(g + 1, 2), 16);
          if (R < 128)
            d += String.fromCharCode(R), g += 3;
          else if (R >= 194 && R < 224) {
            if (k - g >= 6) {
              var ae = parseInt(p.substr(g + 4, 2), 16);
              d += String.fromCharCode((R & 31) << 6 | ae & 63);
            } else
              d += p.substr(g, 6);
            g += 6;
          } else if (R >= 224) {
            if (k - g >= 9) {
              var se = parseInt(p.substr(g + 4, 2), 16), Ee = parseInt(p.substr(g + 7, 2), 16);
              d += String.fromCharCode((R & 15) << 12 | (se & 63) << 6 | Ee & 63);
            } else
              d += p.substr(g, 9);
            g += 9;
          } else
            d += p.substr(g, 3), g += 3;
        }
        return d;
      }
      function j(p, d) {
        function g(k) {
          var R = le(k);
          return R.match(d.UNRESERVED) ? R : k;
        }
        return p.scheme && (p.scheme = String(p.scheme).replace(d.PCT_ENCODED, g).toLowerCase().replace(d.NOT_SCHEME, "")), p.userinfo !== void 0 && (p.userinfo = String(p.userinfo).replace(d.PCT_ENCODED, g).replace(d.NOT_USERINFO, B).replace(d.PCT_ENCODED, i)), p.host !== void 0 && (p.host = String(p.host).replace(d.PCT_ENCODED, g).toLowerCase().replace(d.NOT_HOST, B).replace(d.PCT_ENCODED, i)), p.path !== void 0 && (p.path = String(p.path).replace(d.PCT_ENCODED, g).replace(p.scheme ? d.NOT_PATH : d.NOT_PATH_NOSCHEME, B).replace(d.PCT_ENCODED, i)), p.query !== void 0 && (p.query = String(p.query).replace(d.PCT_ENCODED, g).replace(d.NOT_QUERY, B).replace(d.PCT_ENCODED, i)), p.fragment !== void 0 && (p.fragment = String(p.fragment).replace(d.PCT_ENCODED, g).replace(d.NOT_FRAGMENT, B).replace(d.PCT_ENCODED, i)), p;
      }
      function V(p) {
        return p.replace(/^0*(.*)/, "$1") || "0";
      }
      function ve(p, d) {
        var g = p.match(d.IPV4ADDRESS) || [], k = _(g, 2), R = k[1];
        return R ? R.split(".").map(V).join(".") : p;
      }
      function Se(p, d) {
        var g = p.match(d.IPV6ADDRESS) || [], k = _(g, 3), R = k[1], ae = k[2];
        if (R) {
          for (var se = R.toLowerCase().split("::").reverse(), Ee = _(se, 2), Ie = Ee[0], ze = Ee[1], we = ze ? ze.split(":").map(V) : [], $e = Ie.split(":").map(V), Ue = d.IPV4ADDRESS.test($e[$e.length - 1]), he = Ue ? 7 : 8, De = $e.length - he, He = Array(he), je = 0; je < he; ++je)
            He[je] = we[je] || $e[De + je] || "";
          Ue && (He[he - 1] = ve(He[he - 1], d));
          var Qt = He.reduce(function(vt, At, er) {
            if (!At || At === "0") {
              var qt = vt[vt.length - 1];
              qt && qt.index + qt.length === er ? qt.length++ : vt.push({ index: er, length: 1 });
            }
            return vt;
          }, []), kt = Qt.sort(function(vt, At) {
            return At.length - vt.length;
          })[0], Tt = void 0;
          if (kt && kt.length > 1) {
            var cr = He.slice(0, kt.index), Xt = He.slice(kt.index + kt.length);
            Tt = cr.join(":") + "::" + Xt.join(":");
          } else
            Tt = He.join(":");
          return ae && (Tt += "%" + ae), Tt;
        } else
          return p;
      }
      var be = /^(?:([^:\/?#]+):)?(?:\/\/((?:([^\/?#@]*)@)?(\[[^\/?#\]]+\]|[^\/?#:]*)(?:\:(\d*))?))?([^?#]*)(?:\?([^#]*))?(?:#((?:.|\n|\r)*))?/i, Ne = "".match(/(){0}/)[1] === void 0;
      function Te(p) {
        var d = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {}, g = {}, k = d.iri !== !1 ? m : h;
        d.reference === "suffix" && (p = (d.scheme ? d.scheme + ":" : "") + "//" + p);
        var R = p.match(be);
        if (R) {
          Ne ? (g.scheme = R[1], g.userinfo = R[3], g.host = R[4], g.port = parseInt(R[5], 10), g.path = R[6] || "", g.query = R[7], g.fragment = R[8], isNaN(g.port) && (g.port = R[5])) : (g.scheme = R[1] || void 0, g.userinfo = p.indexOf("@") !== -1 ? R[3] : void 0, g.host = p.indexOf("//") !== -1 ? R[4] : void 0, g.port = parseInt(R[5], 10), g.path = R[6] || "", g.query = p.indexOf("?") !== -1 ? R[7] : void 0, g.fragment = p.indexOf("#") !== -1 ? R[8] : void 0, isNaN(g.port) && (g.port = p.match(/\/\/(?:.|\n)*\:(?:\/|\?|\#|$)/) ? R[4] : void 0)), g.host && (g.host = Se(ve(g.host, k), k)), g.scheme === void 0 && g.userinfo === void 0 && g.host === void 0 && g.port === void 0 && !g.path && g.query === void 0 ? g.reference = "same-document" : g.scheme === void 0 ? g.reference = "relative" : g.fragment === void 0 ? g.reference = "absolute" : g.reference = "uri", d.reference && d.reference !== "suffix" && d.reference !== g.reference && (g.error = g.error || "URI is not a " + d.reference + " reference.");
          var ae = D[(d.scheme || g.scheme || "").toLowerCase()];
          if (!d.unicodeSupport && (!ae || !ae.unicodeSupport)) {
            if (g.host && (d.domainHost || ae && ae.domainHost))
              try {
                g.host = w.toASCII(g.host.replace(k.PCT_ENCODED, le).toLowerCase());
              } catch (se) {
                g.error = g.error || "Host's domain name can not be converted to ASCII via punycode: " + se;
              }
            j(g, h);
          } else
            j(g, k);
          ae && ae.parse && ae.parse(g, d);
        } else
          g.error = g.error || "URI can not be parsed.";
        return g;
      }
      function Ae(p, d) {
        var g = d.iri !== !1 ? m : h, k = [];
        return p.userinfo !== void 0 && (k.push(p.userinfo), k.push("@")), p.host !== void 0 && k.push(Se(ve(String(p.host), g), g).replace(g.IPV6ADDRESS, function(R, ae, se) {
          return "[" + ae + (se ? "%25" + se : "") + "]";
        })), (typeof p.port == "number" || typeof p.port == "string") && (k.push(":"), k.push(String(p.port))), k.length ? k.join("") : void 0;
      }
      var Ye = /^\.\.?\//, Fe = /^\/\.(\/|$)/, wt = /^\/\.\.(\/|$)/, rt = /^\/?(?:.|\n)*?(?=\/|$)/;
      function Le(p) {
        for (var d = []; p.length; )
          if (p.match(Ye))
            p = p.replace(Ye, "");
          else if (p.match(Fe))
            p = p.replace(Fe, "/");
          else if (p.match(wt))
            p = p.replace(wt, "/"), d.pop();
          else if (p === "." || p === "..")
            p = "";
          else {
            var g = p.match(rt);
            if (g) {
              var k = g[0];
              p = p.slice(k.length), d.push(k);
            } else
              throw new Error("Unexpected dot segment condition");
          }
        return d.join("");
      }
      function Me(p) {
        var d = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {}, g = d.iri ? m : h, k = [], R = D[(d.scheme || p.scheme || "").toLowerCase()];
        if (R && R.serialize && R.serialize(p, d), p.host && !g.IPV6ADDRESS.test(p.host)) {
          if (d.domainHost || R && R.domainHost)
            try {
              p.host = d.iri ? w.toUnicode(p.host) : w.toASCII(p.host.replace(g.PCT_ENCODED, le).toLowerCase());
            } catch (Ee) {
              p.error = p.error || "Host's domain name can not be converted to " + (d.iri ? "Unicode" : "ASCII") + " via punycode: " + Ee;
            }
        }
        j(p, g), d.reference !== "suffix" && p.scheme && (k.push(p.scheme), k.push(":"));
        var ae = Ae(p, d);
        if (ae !== void 0 && (d.reference !== "suffix" && k.push("//"), k.push(ae), p.path && p.path.charAt(0) !== "/" && k.push("/")), p.path !== void 0) {
          var se = p.path;
          !d.absolutePath && (!R || !R.absolutePath) && (se = Le(se)), ae === void 0 && (se = se.replace(/^\/\//, "/%2F")), k.push(se);
        }
        return p.query !== void 0 && (k.push("?"), k.push(p.query)), p.fragment !== void 0 && (k.push("#"), k.push(p.fragment)), k.join("");
      }
      function lt(p, d) {
        var g = arguments.length > 2 && arguments[2] !== void 0 ? arguments[2] : {}, k = arguments[3], R = {};
        return k || (p = Te(Me(p, g), g), d = Te(Me(d, g), g)), g = g || {}, !g.tolerant && d.scheme ? (R.scheme = d.scheme, R.userinfo = d.userinfo, R.host = d.host, R.port = d.port, R.path = Le(d.path || ""), R.query = d.query) : (d.userinfo !== void 0 || d.host !== void 0 || d.port !== void 0 ? (R.userinfo = d.userinfo, R.host = d.host, R.port = d.port, R.path = Le(d.path || ""), R.query = d.query) : (d.path ? (d.path.charAt(0) === "/" ? R.path = Le(d.path) : ((p.userinfo !== void 0 || p.host !== void 0 || p.port !== void 0) && !p.path ? R.path = "/" + d.path : p.path ? R.path = p.path.slice(0, p.path.lastIndexOf("/") + 1) + d.path : R.path = d.path, R.path = Le(R.path)), R.query = d.query) : (R.path = p.path, d.query !== void 0 ? R.query = d.query : R.query = p.query), R.userinfo = p.userinfo, R.host = p.host, R.port = p.port), R.scheme = p.scheme), R.fragment = d.fragment, R;
      }
      function Fr(p, d, g) {
        var k = u({ scheme: "null" }, g);
        return Me(lt(Te(p, k), Te(d, k), k, !0), k);
      }
      function va(p, d) {
        return typeof p == "string" ? p = Me(Te(p, d), d) : n(p) === "object" && (p = Te(Me(p, d), d)), p;
      }
      function ga(p, d, g) {
        return typeof p == "string" ? p = Me(Te(p, g), g) : n(p) === "object" && (p = Me(p, g)), typeof d == "string" ? d = Me(Te(d, g), g) : n(d) === "object" && (d = Me(d, g)), p === d;
      }
      function Zo(p, d) {
        return p && p.toString().replace(!d || !d.iri ? h.ESCAPE : m.ESCAPE, B);
      }
      function Ft(p, d) {
        return p && p.toString().replace(!d || !d.iri ? h.PCT_ENCODED : m.PCT_ENCODED, le);
      }
      var _r = {
        scheme: "http",
        domainHost: !0,
        parse: function(d, g) {
          return d.host || (d.error = d.error || "HTTP URIs must have a host."), d;
        },
        serialize: function(d, g) {
          var k = String(d.scheme).toLowerCase() === "https";
          return (d.port === (k ? 443 : 80) || d.port === "") && (d.port = void 0), d.path || (d.path = "/"), d;
        }
      }, un = {
        scheme: "https",
        domainHost: _r.domainHost,
        parse: _r.parse,
        serialize: _r.serialize
      };
      function ln(p) {
        return typeof p.secure == "boolean" ? p.secure : String(p.scheme).toLowerCase() === "wss";
      }
      var br = {
        scheme: "ws",
        domainHost: !0,
        parse: function(d, g) {
          var k = d;
          return k.secure = ln(k), k.resourceName = (k.path || "/") + (k.query ? "?" + k.query : ""), k.path = void 0, k.query = void 0, k;
        },
        serialize: function(d, g) {
          if ((d.port === (ln(d) ? 443 : 80) || d.port === "") && (d.port = void 0), typeof d.secure == "boolean" && (d.scheme = d.secure ? "wss" : "ws", d.secure = void 0), d.resourceName) {
            var k = d.resourceName.split("?"), R = _(k, 2), ae = R[0], se = R[1];
            d.path = ae && ae !== "/" ? ae : void 0, d.query = se, d.resourceName = void 0;
          }
          return d.fragment = void 0, d;
        }
      }, cn = {
        scheme: "wss",
        domainHost: br.domainHost,
        parse: br.parse,
        serialize: br.serialize
      }, Mo = {}, dn = "[A-Za-z0-9\\-\\.\\_\\~\\xA0-\\u200D\\u2010-\\u2029\\u202F-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF]", Rt = "[0-9A-Fa-f]", zo = t(t("%[EFef]" + Rt + "%" + Rt + Rt + "%" + Rt + Rt) + "|" + t("%[89A-Fa-f]" + Rt + "%" + Rt + Rt) + "|" + t("%" + Rt + Rt)), Uo = "[A-Za-z0-9\\!\\$\\%\\'\\*\\+\\-\\^\\_\\`\\{\\|\\}\\~]", Vo = "[\\!\\$\\%\\'\\(\\)\\*\\+\\,\\-\\.0-9\\<\\>A-Z\\x5E-\\x7E]", Ho = a(Vo, '[\\"\\\\]'), Bo = "[\\!\\$\\'\\(\\)\\*\\+\\,\\;\\:\\@]", Qo = new RegExp(dn, "g"), lr = new RegExp(zo, "g"), Ko = new RegExp(a("[^]", Uo, "[\\.]", '[\\"]', Ho), "g"), hn = new RegExp(a("[^]", dn, Bo), "g"), Jo = hn;
      function ya(p) {
        var d = le(p);
        return d.match(Qo) ? d : p;
      }
      var fn = {
        scheme: "mailto",
        parse: function(d, g) {
          var k = d, R = k.to = k.path ? k.path.split(",") : [];
          if (k.path = void 0, k.query) {
            for (var ae = !1, se = {}, Ee = k.query.split("&"), Ie = 0, ze = Ee.length; Ie < ze; ++Ie) {
              var we = Ee[Ie].split("=");
              switch (we[0]) {
                case "to":
                  for (var $e = we[1].split(","), Ue = 0, he = $e.length; Ue < he; ++Ue)
                    R.push($e[Ue]);
                  break;
                case "subject":
                  k.subject = Ft(we[1], g);
                  break;
                case "body":
                  k.body = Ft(we[1], g);
                  break;
                default:
                  ae = !0, se[Ft(we[0], g)] = Ft(we[1], g);
                  break;
              }
            }
            ae && (k.headers = se);
          }
          k.query = void 0;
          for (var De = 0, He = R.length; De < He; ++De) {
            var je = R[De].split("@");
            if (je[0] = Ft(je[0]), g.unicodeSupport)
              je[1] = Ft(je[1], g).toLowerCase();
            else
              try {
                je[1] = w.toASCII(Ft(je[1], g).toLowerCase());
              } catch (Qt) {
                k.error = k.error || "Email address's domain name can not be converted to ASCII via punycode: " + Qt;
              }
            R[De] = je.join("@");
          }
          return k;
        },
        serialize: function(d, g) {
          var k = d, R = o(d.to);
          if (R) {
            for (var ae = 0, se = R.length; ae < se; ++ae) {
              var Ee = String(R[ae]), Ie = Ee.lastIndexOf("@"), ze = Ee.slice(0, Ie).replace(lr, ya).replace(lr, i).replace(Ko, B), we = Ee.slice(Ie + 1);
              try {
                we = g.iri ? w.toUnicode(we) : w.toASCII(Ft(we, g).toLowerCase());
              } catch (De) {
                k.error = k.error || "Email address's domain name can not be converted to " + (g.iri ? "Unicode" : "ASCII") + " via punycode: " + De;
              }
              R[ae] = ze + "@" + we;
            }
            k.path = R.join(",");
          }
          var $e = d.headers = d.headers || {};
          d.subject && ($e.subject = d.subject), d.body && ($e.body = d.body);
          var Ue = [];
          for (var he in $e)
            $e[he] !== Mo[he] && Ue.push(he.replace(lr, ya).replace(lr, i).replace(hn, B) + "=" + $e[he].replace(lr, ya).replace(lr, i).replace(Jo, B));
          return Ue.length && (k.query = Ue.join("&")), k;
        }
      }, Wo = /^([^\:]+)\:(.*)/, pn = {
        scheme: "urn",
        parse: function(d, g) {
          var k = d.path && d.path.match(Wo), R = d;
          if (k) {
            var ae = g.scheme || R.scheme || "urn", se = k[1].toLowerCase(), Ee = k[2], Ie = ae + ":" + (g.nid || se), ze = D[Ie];
            R.nid = se, R.nss = Ee, R.path = void 0, ze && (R = ze.parse(R, g));
          } else
            R.error = R.error || "URN can not be parsed.";
          return R;
        },
        serialize: function(d, g) {
          var k = g.scheme || d.scheme || "urn", R = d.nid, ae = k + ":" + (g.nid || R), se = D[ae];
          se && (d = se.serialize(d, g));
          var Ee = d, Ie = d.nss;
          return Ee.path = (R || g.nid) + ":" + Ie, Ee;
        }
      }, Go = /^[0-9A-Fa-f]{8}(?:\-[0-9A-Fa-f]{4}){3}\-[0-9A-Fa-f]{12}$/, mn = {
        scheme: "urn:uuid",
        parse: function(d, g) {
          var k = d;
          return k.uuid = k.nss, k.nss = void 0, !g.tolerant && (!k.uuid || !k.uuid.match(Go)) && (k.error = k.error || "UUID is not valid."), k;
        },
        serialize: function(d, g) {
          var k = d;
          return k.nss = (d.uuid || "").toLowerCase(), k;
        }
      };
      D[_r.scheme] = _r, D[un.scheme] = un, D[br.scheme] = br, D[cn.scheme] = cn, D[fn.scheme] = fn, D[pn.scheme] = pn, D[mn.scheme] = mn, r.SCHEMES = D, r.pctEncChar = B, r.pctDecChars = le, r.parse = Te, r.removeDotSegments = Le, r.serialize = Me, r.resolveComponents = lt, r.resolve = Fr, r.normalize = va, r.equal = ga, r.escapeComponent = Zo, r.unescapeComponent = Ft, Object.defineProperty(r, "__esModule", { value: !0 });
    });
  }(xr, xr.exports)), xr.exports;
}
var Pa, Vn;
function sn() {
  return Vn || (Vn = 1, Pa = function s(e, r) {
    if (e === r) return !0;
    if (e && r && typeof e == "object" && typeof r == "object") {
      if (e.constructor !== r.constructor) return !1;
      var a, t, n;
      if (Array.isArray(e)) {
        if (a = e.length, a != r.length) return !1;
        for (t = a; t-- !== 0; )
          if (!s(e[t], r[t])) return !1;
        return !0;
      }
      if (e.constructor === RegExp) return e.source === r.source && e.flags === r.flags;
      if (e.valueOf !== Object.prototype.valueOf) return e.valueOf() === r.valueOf();
      if (e.toString !== Object.prototype.toString) return e.toString() === r.toString();
      if (n = Object.keys(e), a = n.length, a !== Object.keys(r).length) return !1;
      for (t = a; t-- !== 0; )
        if (!Object.prototype.hasOwnProperty.call(r, n[t])) return !1;
      for (t = a; t-- !== 0; ) {
        var i = n[t];
        if (!s(e[i], r[i])) return !1;
      }
      return !0;
    }
    return e !== e && r !== r;
  }), Pa;
}
var Sa, Hn;
function Yc() {
  return Hn || (Hn = 1, Sa = function(e) {
    for (var r = 0, a = e.length, t = 0, n; t < a; )
      r++, n = e.charCodeAt(t++), n >= 55296 && n <= 56319 && t < a && (n = e.charCodeAt(t), (n & 64512) == 56320 && t++);
    return r;
  }), Sa;
}
var wa, Bn;
function yr() {
  if (Bn) return wa;
  Bn = 1, wa = {
    copy: s,
    checkDataType: e,
    checkDataTypes: r,
    coerceToTypes: t,
    toHash: n,
    getProperty: u,
    escapeQuotes: l,
    equal: sn(),
    ucs2length: Yc(),
    varOccurences: h,
    varReplace: m,
    schemaHasRules: _,
    schemaHasRulesExcept: c,
    schemaUnknownRules: f,
    toQuotedString: y,
    getPathExpr: v,
    getPath: P,
    getData: b,
    unescapeFragment: T,
    unescapeJsonPointer: $,
    escapeFragment: O,
    escapeJsonPointer: C
  };
  function s(S, E) {
    E = E || {};
    for (var N in S) E[N] = S[N];
    return E;
  }
  function e(S, E, N, L) {
    var Z = L ? " !== " : " === ", re = L ? " || " : " && ", fe = L ? "!" : "", ne = L ? "" : "!";
    switch (S) {
      case "null":
        return E + Z + "null";
      case "array":
        return fe + "Array.isArray(" + E + ")";
      case "object":
        return "(" + fe + E + re + "typeof " + E + Z + '"object"' + re + ne + "Array.isArray(" + E + "))";
      case "integer":
        return "(typeof " + E + Z + '"number"' + re + ne + "(" + E + " % 1)" + re + E + Z + E + (N ? re + fe + "isFinite(" + E + ")" : "") + ")";
      case "number":
        return "(typeof " + E + Z + '"' + S + '"' + (N ? re + fe + "isFinite(" + E + ")" : "") + ")";
      default:
        return "typeof " + E + Z + '"' + S + '"';
    }
  }
  function r(S, E, N) {
    switch (S.length) {
      case 1:
        return e(S[0], E, N, !0);
      default:
        var L = "", Z = n(S);
        Z.array && Z.object && (L = Z.null ? "(" : "(!" + E + " || ", L += "typeof " + E + ' !== "object")', delete Z.null, delete Z.array, delete Z.object), Z.number && delete Z.integer;
        for (var re in Z)
          L += (L ? " && " : "") + e(re, E, N, !0);
        return L;
    }
  }
  var a = n(["string", "number", "integer", "boolean", "null"]);
  function t(S, E) {
    if (Array.isArray(E)) {
      for (var N = [], L = 0; L < E.length; L++) {
        var Z = E[L];
        (a[Z] || S === "array" && Z === "array") && (N[N.length] = Z);
      }
      if (N.length) return N;
    } else {
      if (a[E])
        return [E];
      if (S === "array" && E === "array")
        return ["array"];
    }
  }
  function n(S) {
    for (var E = {}, N = 0; N < S.length; N++) E[S[N]] = !0;
    return E;
  }
  var i = /^[a-z$_][a-z$_0-9]*$/i, o = /'|\\/g;
  function u(S) {
    return typeof S == "number" ? "[" + S + "]" : i.test(S) ? "." + S : "['" + l(S) + "']";
  }
  function l(S) {
    return S.replace(o, "\\$&").replace(/\n/g, "\\n").replace(/\r/g, "\\r").replace(/\f/g, "\\f").replace(/\t/g, "\\t");
  }
  function h(S, E) {
    E += "[^0-9]";
    var N = S.match(new RegExp(E, "g"));
    return N ? N.length : 0;
  }
  function m(S, E, N) {
    return E += "([^0-9])", N = N.replace(/\$/g, "$$$$"), S.replace(new RegExp(E, "g"), N + "$1");
  }
  function _(S, E) {
    if (typeof S == "boolean") return !S;
    for (var N in S) if (E[N]) return !0;
  }
  function c(S, E, N) {
    if (typeof S == "boolean") return !S && N != "not";
    for (var L in S) if (L != N && E[L]) return !0;
  }
  function f(S, E) {
    if (typeof S != "boolean") {
      for (var N in S) if (!E[N]) return N;
    }
  }
  function y(S) {
    return "'" + l(S) + "'";
  }
  function v(S, E, N, L) {
    var Z = N ? "'/' + " + E + (L ? "" : ".replace(/~/g, '~0').replace(/\\//g, '~1')") : L ? "'[' + " + E + " + ']'" : "'[\\'' + " + E + " + '\\']'";
    return x(S, Z);
  }
  function P(S, E, N) {
    var L = y(N ? "/" + C(E) : u(E));
    return x(S, L);
  }
  var I = /^\/(?:[^~]|~0|~1)*$/, A = /^([0-9]+)(#|\/(?:[^~]|~0|~1)*)?$/;
  function b(S, E, N) {
    var L, Z, re, fe;
    if (S === "") return "rootData";
    if (S[0] == "/") {
      if (!I.test(S)) throw new Error("Invalid JSON-pointer: " + S);
      Z = S, re = "rootData";
    } else {
      if (fe = S.match(A), !fe) throw new Error("Invalid JSON-pointer: " + S);
      if (L = +fe[1], Z = fe[2], Z == "#") {
        if (L >= E) throw new Error("Cannot access property/index " + L + " levels up, current level is " + E);
        return N[E - L];
      }
      if (L > E) throw new Error("Cannot access data " + L + " levels up, current level is " + E);
      if (re = "data" + (E - L || ""), !Z) return re;
    }
    for (var ne = re, _e = Z.split("/"), ce = 0; ce < _e.length; ce++) {
      var de = _e[ce];
      de && (re += u($(de)), ne += " && " + re);
    }
    return ne;
  }
  function x(S, E) {
    return S == '""' ? E : (S + " + " + E).replace(/([^\\])' \+ '/g, "$1");
  }
  function T(S) {
    return $(decodeURIComponent(S));
  }
  function O(S) {
    return encodeURIComponent(C(S));
  }
  function C(S) {
    return S.replace(/~/g, "~0").replace(/\//g, "~1");
  }
  function $(S) {
    return S.replace(/~1/g, "/").replace(/~0/g, "~");
  }
  return wa;
}
var xa, Qn;
function To() {
  if (Qn) return xa;
  Qn = 1;
  var s = yr();
  xa = e;
  function e(r) {
    s.copy(r, this);
  }
  return xa;
}
var Ea = { exports: {} }, Kn;
function Xc() {
  if (Kn) return Ea.exports;
  Kn = 1;
  var s = Ea.exports = function(a, t, n) {
    typeof t == "function" && (n = t, t = {}), n = t.cb || n;
    var i = typeof n == "function" ? n : n.pre || function() {
    }, o = n.post || function() {
    };
    e(t, i, o, a, "", a);
  };
  s.keywords = {
    additionalItems: !0,
    items: !0,
    contains: !0,
    additionalProperties: !0,
    propertyNames: !0,
    not: !0
  }, s.arrayKeywords = {
    items: !0,
    allOf: !0,
    anyOf: !0,
    oneOf: !0
  }, s.propsKeywords = {
    definitions: !0,
    properties: !0,
    patternProperties: !0,
    dependencies: !0
  }, s.skipKeywords = {
    default: !0,
    enum: !0,
    const: !0,
    required: !0,
    maximum: !0,
    minimum: !0,
    exclusiveMaximum: !0,
    exclusiveMinimum: !0,
    multipleOf: !0,
    maxLength: !0,
    minLength: !0,
    pattern: !0,
    format: !0,
    maxItems: !0,
    minItems: !0,
    uniqueItems: !0,
    maxProperties: !0,
    minProperties: !0
  };
  function e(a, t, n, i, o, u, l, h, m, _) {
    if (i && typeof i == "object" && !Array.isArray(i)) {
      t(i, o, u, l, h, m, _);
      for (var c in i) {
        var f = i[c];
        if (Array.isArray(f)) {
          if (c in s.arrayKeywords)
            for (var y = 0; y < f.length; y++)
              e(a, t, n, f[y], o + "/" + c + "/" + y, u, o, c, i, y);
        } else if (c in s.propsKeywords) {
          if (f && typeof f == "object")
            for (var v in f)
              e(a, t, n, f[v], o + "/" + c + "/" + r(v), u, o, c, i, v);
        } else (c in s.keywords || a.allKeys && !(c in s.skipKeywords)) && e(a, t, n, f, o + "/" + c, u, o, c, i);
      }
      n(i, o, u, l, h, m, _);
    }
  }
  function r(a) {
    return a.replace(/~/g, "~0").replace(/\//g, "~1");
  }
  return Ea.exports;
}
var Ra, Jn;
function nn() {
  if (Jn) return Ra;
  Jn = 1;
  var s = Gc(), e = sn(), r = yr(), a = To(), t = Xc();
  Ra = n, n.normalizeId = P, n.fullPath = f, n.url = I, n.ids = A, n.inlineRef = m, n.schema = i;
  function n(b, x, T) {
    var O = this._refs[T];
    if (typeof O == "string")
      if (this._refs[O]) O = this._refs[O];
      else return n.call(this, b, x, O);
    if (O = O || this._schemas[T], O instanceof a)
      return m(O.schema, this._opts.inlineRefs) ? O.schema : O.validate || this._compile(O);
    var C = i.call(this, x, T), $, S, E;
    return C && ($ = C.schema, x = C.root, E = C.baseId), $ instanceof a ? S = $.validate || b.call(this, $.schema, x, void 0, E) : $ !== void 0 && (S = m($, this._opts.inlineRefs) ? $ : b.call(this, $, x, void 0, E)), S;
  }
  function i(b, x) {
    var T = s.parse(x), O = y(T), C = f(this._getId(b.schema));
    if (Object.keys(b.schema).length === 0 || O !== C) {
      var $ = P(O), S = this._refs[$];
      if (typeof S == "string")
        return o.call(this, b, S, T);
      if (S instanceof a)
        S.validate || this._compile(S), b = S;
      else if (S = this._schemas[$], S instanceof a) {
        if (S.validate || this._compile(S), $ == P(x))
          return { schema: S, root: b, baseId: C };
        b = S;
      } else
        return;
      if (!b.schema) return;
      C = f(this._getId(b.schema));
    }
    return l.call(this, T, C, b.schema, b);
  }
  function o(b, x, T) {
    var O = i.call(this, b, x);
    if (O) {
      var C = O.schema, $ = O.baseId;
      b = O.root;
      var S = this._getId(C);
      return S && ($ = I($, S)), l.call(this, T, $, C, b);
    }
  }
  var u = r.toHash(["properties", "patternProperties", "enum", "dependencies", "definitions"]);
  function l(b, x, T, O) {
    if (b.fragment = b.fragment || "", b.fragment.slice(0, 1) == "/") {
      for (var C = b.fragment.split("/"), $ = 1; $ < C.length; $++) {
        var S = C[$];
        if (S) {
          if (S = r.unescapeFragment(S), T = T[S], T === void 0) break;
          var E;
          if (!u[S] && (E = this._getId(T), E && (x = I(x, E)), T.$ref)) {
            var N = I(x, T.$ref), L = i.call(this, O, N);
            L && (T = L.schema, O = L.root, x = L.baseId);
          }
        }
      }
      if (T !== void 0 && T !== O.schema)
        return { schema: T, root: O, baseId: x };
    }
  }
  var h = r.toHash([
    "type",
    "format",
    "pattern",
    "maxLength",
    "minLength",
    "maxProperties",
    "minProperties",
    "maxItems",
    "minItems",
    "maximum",
    "minimum",
    "uniqueItems",
    "multipleOf",
    "required",
    "enum"
  ]);
  function m(b, x) {
    if (x === !1) return !1;
    if (x === void 0 || x === !0) return _(b);
    if (x) return c(b) <= x;
  }
  function _(b) {
    var x;
    if (Array.isArray(b)) {
      for (var T = 0; T < b.length; T++)
        if (x = b[T], typeof x == "object" && !_(x)) return !1;
    } else
      for (var O in b)
        if (O == "$ref" || (x = b[O], typeof x == "object" && !_(x))) return !1;
    return !0;
  }
  function c(b) {
    var x = 0, T;
    if (Array.isArray(b)) {
      for (var O = 0; O < b.length; O++)
        if (T = b[O], typeof T == "object" && (x += c(T)), x == 1 / 0) return 1 / 0;
    } else
      for (var C in b) {
        if (C == "$ref") return 1 / 0;
        if (h[C])
          x++;
        else if (T = b[C], typeof T == "object" && (x += c(T) + 1), x == 1 / 0) return 1 / 0;
      }
    return x;
  }
  function f(b, x) {
    x !== !1 && (b = P(b));
    var T = s.parse(b);
    return y(T);
  }
  function y(b) {
    return s.serialize(b).split("#")[0] + "#";
  }
  var v = /#\/?$/;
  function P(b) {
    return b ? b.replace(v, "") : "";
  }
  function I(b, x) {
    return x = P(x), s.resolve(b, x);
  }
  function A(b) {
    var x = P(this._getId(b)), T = { "": x }, O = { "": f(x, !1) }, C = {}, $ = this;
    return t(b, { allKeys: !0 }, function(S, E, N, L, Z, re, fe) {
      if (E !== "") {
        var ne = $._getId(S), _e = T[L], ce = O[L] + "/" + Z;
        if (fe !== void 0 && (ce += "/" + (typeof fe == "number" ? fe : r.escapeFragment(fe))), typeof ne == "string") {
          ne = _e = P(_e ? s.resolve(_e, ne) : ne);
          var de = $._refs[ne];
          if (typeof de == "string" && (de = $._refs[de]), de && de.schema) {
            if (!e(S, de.schema))
              throw new Error('id "' + ne + '" resolves to more than one schema');
          } else if (ne != P(ce))
            if (ne[0] == "#") {
              if (C[ne] && !e(S, C[ne]))
                throw new Error('id "' + ne + '" resolves to more than one schema');
              C[ne] = S;
            } else
              $._refs[ne] = ce;
        }
        T[E] = _e, O[E] = ce;
      }
    }), C;
  }
  return Ra;
}
var ka, Wn;
function on() {
  if (Wn) return ka;
  Wn = 1;
  var s = nn();
  ka = {
    Validation: a(e),
    MissingRef: a(r)
  };
  function e(t) {
    this.message = "validation failed", this.errors = t, this.ajv = this.validation = !0;
  }
  r.message = function(t, n) {
    return "can't resolve reference " + n + " from id " + t;
  };
  function r(t, n, i) {
    this.message = i || r.message(t, n), this.missingRef = s.url(t, n), this.missingSchema = s.normalizeId(s.fullPath(this.missingRef));
  }
  function a(t) {
    return t.prototype = Object.create(Error.prototype), t.prototype.constructor = t, t;
  }
  return ka;
}
var Ta, Gn;
function Ao() {
  return Gn || (Gn = 1, Ta = function(s, e) {
    e || (e = {}), typeof e == "function" && (e = { cmp: e });
    var r = typeof e.cycles == "boolean" ? e.cycles : !1, a = e.cmp && /* @__PURE__ */ function(n) {
      return function(i) {
        return function(o, u) {
          var l = { key: o, value: i[o] }, h = { key: u, value: i[u] };
          return n(l, h);
        };
      };
    }(e.cmp), t = [];
    return function n(i) {
      if (i && i.toJSON && typeof i.toJSON == "function" && (i = i.toJSON()), i !== void 0) {
        if (typeof i == "number") return isFinite(i) ? "" + i : "null";
        if (typeof i != "object") return JSON.stringify(i);
        var o, u;
        if (Array.isArray(i)) {
          for (u = "[", o = 0; o < i.length; o++)
            o && (u += ","), u += n(i[o]) || "null";
          return u + "]";
        }
        if (i === null) return "null";
        if (t.indexOf(i) !== -1) {
          if (r) return JSON.stringify("__cycle__");
          throw new TypeError("Converting circular structure to JSON");
        }
        var l = t.push(i) - 1, h = Object.keys(i).sort(a && a(i));
        for (u = "", o = 0; o < h.length; o++) {
          var m = h[o], _ = n(i[m]);
          _ && (u && (u += ","), u += JSON.stringify(m) + ":" + _);
        }
        return t.splice(l, 1), "{" + u + "}";
      }
    }(s);
  }), Ta;
}
var Aa, Yn;
function Oo() {
  return Yn || (Yn = 1, Aa = function(e, r, a) {
    var t = "", n = e.schema.$async === !0, i = e.util.schemaHasRulesExcept(e.schema, e.RULES.all, "$ref"), o = e.self._getId(e.schema);
    if (e.opts.strictKeywords) {
      var u = e.util.schemaUnknownRules(e.schema, e.RULES.keywords);
      if (u) {
        var l = "unknown keyword: " + u;
        if (e.opts.strictKeywords === "log") e.logger.warn(l);
        else throw new Error(l);
      }
    }
    if (e.isTop && (t += " var validate = ", n && (e.async = !0, t += "async "), t += "function(data, dataPath, parentData, parentDataProperty, rootData) { 'use strict'; ", o && (e.opts.sourceCode || e.opts.processCode) && (t += " " + ("/*# sourceURL=" + o + " */") + " ")), typeof e.schema == "boolean" || !(i || e.schema.$ref)) {
      var r = "false schema", h = e.level, m = e.dataLevel, _ = e.schema[r], c = e.schemaPath + e.util.getProperty(r), f = e.errSchemaPath + "/" + r, x = !e.opts.allErrors, C, y = "data" + (m || ""), b = "valid" + h;
      if (e.schema === !1) {
        e.isTop ? x = !0 : t += " var " + b + " = false; ";
        var v = v || [];
        v.push(t), t = "", e.createErrors !== !1 ? (t += " { keyword: '" + (C || "false schema") + "' , dataPath: (dataPath || '') + " + e.errorPath + " , schemaPath: " + e.util.toQuotedString(f) + " , params: {} ", e.opts.messages !== !1 && (t += " , message: 'boolean schema is false' "), e.opts.verbose && (t += " , schema: false , parentSchema: validate.schema" + e.schemaPath + " , data: " + y + " "), t += " } ") : t += " {} ";
        var P = t;
        t = v.pop(), !e.compositeRule && x ? e.async ? t += " throw new ValidationError([" + P + "]); " : t += " validate.errors = [" + P + "]; return false; " : t += " var err = " + P + ";  if (vErrors === null) vErrors = [err]; else vErrors.push(err); errors++; ";
      } else
        e.isTop ? n ? t += " return data; " : t += " validate.errors = null; return true; " : t += " var " + b + " = true; ";
      return e.isTop && (t += " }; return validate; "), t;
    }
    if (e.isTop) {
      var I = e.isTop, h = e.level = 0, m = e.dataLevel = 0, y = "data";
      if (e.rootId = e.resolve.fullPath(e.self._getId(e.root.schema)), e.baseId = e.baseId || e.rootId, delete e.isTop, e.dataPathArr = [""], e.schema.default !== void 0 && e.opts.useDefaults && e.opts.strictDefaults) {
        var A = "default is ignored in the schema root";
        if (e.opts.strictDefaults === "log") e.logger.warn(A);
        else throw new Error(A);
      }
      t += " var vErrors = null; ", t += " var errors = 0;     ", t += " if (rootData === undefined) rootData = data; ";
    } else {
      var h = e.level, m = e.dataLevel, y = "data" + (m || "");
      if (o && (e.baseId = e.resolve.url(e.baseId, o)), n && !e.async) throw new Error("async schema in sync schema");
      t += " var errs_" + h + " = errors;";
    }
    var b = "valid" + h, x = !e.opts.allErrors, T = "", O = "", C, $ = e.schema.type, S = Array.isArray($);
    if ($ && e.opts.nullable && e.schema.nullable === !0 && (S ? $.indexOf("null") == -1 && ($ = $.concat("null")) : $ != "null" && ($ = [$, "null"], S = !0)), S && $.length == 1 && ($ = $[0], S = !1), e.schema.$ref && i) {
      if (e.opts.extendRefs == "fail")
        throw new Error('$ref: validation keywords used in schema at path "' + e.errSchemaPath + '" (see option extendRefs)');
      e.opts.extendRefs !== !0 && (i = !1, e.logger.warn('$ref: keywords ignored in schema at path "' + e.errSchemaPath + '"'));
    }
    if (e.schema.$comment && e.opts.$comment && (t += " " + e.RULES.all.$comment.code(e, "$comment")), $) {
      if (e.opts.coerceTypes)
        var E = e.util.coerceToTypes(e.opts.coerceTypes, $);
      var N = e.RULES.types[$];
      if (E || S || N === !0 || N && !Fe(N)) {
        var c = e.schemaPath + ".type", f = e.errSchemaPath + "/type", c = e.schemaPath + ".type", f = e.errSchemaPath + "/type", L = S ? "checkDataTypes" : "checkDataType";
        if (t += " if (" + e.util[L]($, y, e.opts.strictNumbers, !0) + ") { ", E) {
          var Z = "dataType" + h, re = "coerced" + h;
          t += " var " + Z + " = typeof " + y + "; var " + re + " = undefined; ", e.opts.coerceTypes == "array" && (t += " if (" + Z + " == 'object' && Array.isArray(" + y + ") && " + y + ".length == 1) { " + y + " = " + y + "[0]; " + Z + " = typeof " + y + "; if (" + e.util.checkDataType(e.schema.type, y, e.opts.strictNumbers) + ") " + re + " = " + y + "; } "), t += " if (" + re + " !== undefined) ; ";
          var fe = E;
          if (fe)
            for (var ne, _e = -1, ce = fe.length - 1; _e < ce; )
              ne = fe[_e += 1], ne == "string" ? t += " else if (" + Z + " == 'number' || " + Z + " == 'boolean') " + re + " = '' + " + y + "; else if (" + y + " === null) " + re + " = ''; " : ne == "number" || ne == "integer" ? (t += " else if (" + Z + " == 'boolean' || " + y + " === null || (" + Z + " == 'string' && " + y + " && " + y + " == +" + y + " ", ne == "integer" && (t += " && !(" + y + " % 1)"), t += ")) " + re + " = +" + y + "; ") : ne == "boolean" ? t += " else if (" + y + " === 'false' || " + y + " === 0 || " + y + " === null) " + re + " = false; else if (" + y + " === 'true' || " + y + " === 1) " + re + " = true; " : ne == "null" ? t += " else if (" + y + " === '' || " + y + " === 0 || " + y + " === false) " + re + " = null; " : e.opts.coerceTypes == "array" && ne == "array" && (t += " else if (" + Z + " == 'string' || " + Z + " == 'number' || " + Z + " == 'boolean' || " + y + " == null) " + re + " = [" + y + "]; ");
          t += " else {   ";
          var v = v || [];
          v.push(t), t = "", e.createErrors !== !1 ? (t += " { keyword: '" + (C || "type") + "' , dataPath: (dataPath || '') + " + e.errorPath + " , schemaPath: " + e.util.toQuotedString(f) + " , params: { type: '", S ? t += "" + $.join(",") : t += "" + $, t += "' } ", e.opts.messages !== !1 && (t += " , message: 'should be ", S ? t += "" + $.join(",") : t += "" + $, t += "' "), e.opts.verbose && (t += " , schema: validate.schema" + c + " , parentSchema: validate.schema" + e.schemaPath + " , data: " + y + " "), t += " } ") : t += " {} ";
          var P = t;
          t = v.pop(), !e.compositeRule && x ? e.async ? t += " throw new ValidationError([" + P + "]); " : t += " validate.errors = [" + P + "]; return false; " : t += " var err = " + P + ";  if (vErrors === null) vErrors = [err]; else vErrors.push(err); errors++; ", t += " } if (" + re + " !== undefined) {  ";
          var de = m ? "data" + (m - 1 || "") : "parentData", tt = m ? e.dataPathArr[m] : "parentDataProperty";
          t += " " + y + " = " + re + "; ", m || (t += "if (" + de + " !== undefined)"), t += " " + de + "[" + tt + "] = " + re + "; } ";
        } else {
          var v = v || [];
          v.push(t), t = "", e.createErrors !== !1 ? (t += " { keyword: '" + (C || "type") + "' , dataPath: (dataPath || '') + " + e.errorPath + " , schemaPath: " + e.util.toQuotedString(f) + " , params: { type: '", S ? t += "" + $.join(",") : t += "" + $, t += "' } ", e.opts.messages !== !1 && (t += " , message: 'should be ", S ? t += "" + $.join(",") : t += "" + $, t += "' "), e.opts.verbose && (t += " , schema: validate.schema" + c + " , parentSchema: validate.schema" + e.schemaPath + " , data: " + y + " "), t += " } ") : t += " {} ";
          var P = t;
          t = v.pop(), !e.compositeRule && x ? e.async ? t += " throw new ValidationError([" + P + "]); " : t += " validate.errors = [" + P + "]; return false; " : t += " var err = " + P + ";  if (vErrors === null) vErrors = [err]; else vErrors.push(err); errors++; ";
        }
        t += " } ";
      }
    }
    if (e.schema.$ref && !i)
      t += " " + e.RULES.all.$ref.code(e, "$ref") + " ", x && (t += " } if (errors === ", I ? t += "0" : t += "errs_" + h, t += ") { ", O += "}");
    else {
      var Ge = e.RULES;
      if (Ge) {
        for (var N, Je = -1, Ze = Ge.length - 1; Je < Ze; )
          if (N = Ge[Je += 1], Fe(N)) {
            if (N.type && (t += " if (" + e.util.checkDataType(N.type, y, e.opts.strictNumbers) + ") { "), e.opts.useDefaults) {
              if (N.type == "object" && e.schema.properties) {
                var _ = e.schema.properties, mt = Object.keys(_), w = mt;
                if (w)
                  for (var D, B = -1, le = w.length - 1; B < le; ) {
                    D = w[B += 1];
                    var j = _[D];
                    if (j.default !== void 0) {
                      var V = y + e.util.getProperty(D);
                      if (e.compositeRule) {
                        if (e.opts.strictDefaults) {
                          var A = "default is ignored for: " + V;
                          if (e.opts.strictDefaults === "log") e.logger.warn(A);
                          else throw new Error(A);
                        }
                      } else
                        t += " if (" + V + " === undefined ", e.opts.useDefaults == "empty" && (t += " || " + V + " === null || " + V + " === '' "), t += " ) " + V + " = ", e.opts.useDefaults == "shared" ? t += " " + e.useDefault(j.default) + " " : t += " " + JSON.stringify(j.default) + " ", t += "; ";
                    }
                  }
              } else if (N.type == "array" && Array.isArray(e.schema.items)) {
                var ve = e.schema.items;
                if (ve) {
                  for (var j, _e = -1, Se = ve.length - 1; _e < Se; )
                    if (j = ve[_e += 1], j.default !== void 0) {
                      var V = y + "[" + _e + "]";
                      if (e.compositeRule) {
                        if (e.opts.strictDefaults) {
                          var A = "default is ignored for: " + V;
                          if (e.opts.strictDefaults === "log") e.logger.warn(A);
                          else throw new Error(A);
                        }
                      } else
                        t += " if (" + V + " === undefined ", e.opts.useDefaults == "empty" && (t += " || " + V + " === null || " + V + " === '' "), t += " ) " + V + " = ", e.opts.useDefaults == "shared" ? t += " " + e.useDefault(j.default) + " " : t += " " + JSON.stringify(j.default) + " ", t += "; ";
                    }
                }
              }
            }
            var be = N.rules;
            if (be) {
              for (var Ne, Te = -1, Ae = be.length - 1; Te < Ae; )
                if (Ne = be[Te += 1], wt(Ne)) {
                  var Ye = Ne.code(e, Ne.keyword, N.type);
                  Ye && (t += " " + Ye + " ", x && (T += "}"));
                }
            }
            if (x && (t += " " + T + " ", T = ""), N.type && (t += " } ", $ && $ === N.type && !E)) {
              t += " else { ";
              var c = e.schemaPath + ".type", f = e.errSchemaPath + "/type", v = v || [];
              v.push(t), t = "", e.createErrors !== !1 ? (t += " { keyword: '" + (C || "type") + "' , dataPath: (dataPath || '') + " + e.errorPath + " , schemaPath: " + e.util.toQuotedString(f) + " , params: { type: '", S ? t += "" + $.join(",") : t += "" + $, t += "' } ", e.opts.messages !== !1 && (t += " , message: 'should be ", S ? t += "" + $.join(",") : t += "" + $, t += "' "), e.opts.verbose && (t += " , schema: validate.schema" + c + " , parentSchema: validate.schema" + e.schemaPath + " , data: " + y + " "), t += " } ") : t += " {} ";
              var P = t;
              t = v.pop(), !e.compositeRule && x ? e.async ? t += " throw new ValidationError([" + P + "]); " : t += " validate.errors = [" + P + "]; return false; " : t += " var err = " + P + ";  if (vErrors === null) vErrors = [err]; else vErrors.push(err); errors++; ", t += " } ";
            }
            x && (t += " if (errors === ", I ? t += "0" : t += "errs_" + h, t += ") { ", O += "}");
          }
      }
    }
    x && (t += " " + O + " "), I ? (n ? (t += " if (errors === 0) return data;           ", t += " else throw new ValidationError(vErrors); ") : (t += " validate.errors = vErrors; ", t += " return errors === 0;       "), t += " }; return validate;") : t += " var " + b + " = errors === errs_" + h + ";";
    function Fe(Le) {
      for (var Me = Le.rules, lt = 0; lt < Me.length; lt++)
        if (wt(Me[lt])) return !0;
    }
    function wt(Le) {
      return e.schema[Le.keyword] !== void 0 || Le.implements && rt(Le);
    }
    function rt(Le) {
      for (var Me = Le.implements, lt = 0; lt < Me.length; lt++)
        if (e.schema[Me[lt]] !== void 0) return !0;
    }
    return t;
  }), Aa;
}
var Oa, Xn;
function ed() {
  if (Xn) return Oa;
  Xn = 1;
  var s = nn(), e = yr(), r = on(), a = Ao(), t = Oo(), n = e.ucs2length, i = sn(), o = r.Validation;
  Oa = u;
  function u(P, I, A, b) {
    var x = this, T = this._opts, O = [void 0], C = {}, $ = [], S = {}, E = [], N = {}, L = [];
    I = I || { schema: P, refVal: O, refs: C };
    var Z = l.call(this, P, I, b), re = this._compilations[Z.index];
    if (Z.compiling) return re.callValidate = de;
    var fe = this._formats, ne = this.RULES;
    try {
      var _e = tt(P, I, A, b);
      re.validate = _e;
      var ce = re.callValidate;
      return ce && (ce.schema = _e.schema, ce.errors = null, ce.refs = _e.refs, ce.refVal = _e.refVal, ce.root = _e.root, ce.$async = _e.$async, T.sourceCode && (ce.source = _e.source)), _e;
    } finally {
      h.call(this, P, I, b);
    }
    function de() {
      var j = re.validate, V = j.apply(this, arguments);
      return de.errors = j.errors, V;
    }
    function tt(j, V, ve, Se) {
      var be = !V || V && V.schema == j;
      if (V.schema != I.schema)
        return u.call(x, j, V, ve, Se);
      var Ne = j.$async === !0, Te = t({
        isTop: !0,
        schema: j,
        isRoot: be,
        baseId: Se,
        root: V,
        schemaPath: "",
        errSchemaPath: "#",
        errorPath: '""',
        MissingRefError: r.MissingRef,
        RULES: ne,
        validate: t,
        util: e,
        resolve: s,
        resolveRef: Ge,
        usePattern: D,
        useDefault: B,
        useCustomRule: le,
        opts: T,
        formats: fe,
        logger: x.logger,
        self: x
      });
      Te = v(O, f) + v($, _) + v(E, c) + v(L, y) + Te, T.processCode && (Te = T.processCode(Te, j));
      var Ae;
      try {
        var Ye = new Function(
          "self",
          "RULES",
          "formats",
          "root",
          "refVal",
          "defaults",
          "customRules",
          "equal",
          "ucs2length",
          "ValidationError",
          Te
        );
        Ae = Ye(
          x,
          ne,
          fe,
          I,
          O,
          E,
          L,
          i,
          n,
          o
        ), O[0] = Ae;
      } catch (Fe) {
        throw x.logger.error("Error compiling schema, function code:", Te), Fe;
      }
      return Ae.schema = j, Ae.errors = null, Ae.refs = C, Ae.refVal = O, Ae.root = be ? Ae : V, Ne && (Ae.$async = !0), T.sourceCode === !0 && (Ae.source = {
        code: Te,
        patterns: $,
        defaults: E
      }), Ae;
    }
    function Ge(j, V, ve) {
      V = s.url(j, V);
      var Se = C[V], be, Ne;
      if (Se !== void 0)
        return be = O[Se], Ne = "refVal[" + Se + "]", w(be, Ne);
      if (!ve && I.refs) {
        var Te = I.refs[V];
        if (Te !== void 0)
          return be = I.refVal[Te], Ne = Je(V, be), w(be, Ne);
      }
      Ne = Je(V);
      var Ae = s.call(x, tt, I, V);
      if (Ae === void 0) {
        var Ye = A && A[V];
        Ye && (Ae = s.inlineRef(Ye, T.inlineRefs) ? Ye : u.call(x, Ye, I, A, j));
      }
      if (Ae === void 0)
        Ze(V);
      else
        return mt(V, Ae), w(Ae, Ne);
    }
    function Je(j, V) {
      var ve = O.length;
      return O[ve] = V, C[j] = ve, "refVal" + ve;
    }
    function Ze(j) {
      delete C[j];
    }
    function mt(j, V) {
      var ve = C[j];
      O[ve] = V;
    }
    function w(j, V) {
      return typeof j == "object" || typeof j == "boolean" ? { code: V, schema: j, inline: !0 } : { code: V, $async: j && !!j.$async };
    }
    function D(j) {
      var V = S[j];
      return V === void 0 && (V = S[j] = $.length, $[V] = j), "pattern" + V;
    }
    function B(j) {
      switch (typeof j) {
        case "boolean":
        case "number":
          return "" + j;
        case "string":
          return e.toQuotedString(j);
        case "object":
          if (j === null) return "null";
          var V = a(j), ve = N[V];
          return ve === void 0 && (ve = N[V] = E.length, E[ve] = j), "default" + ve;
      }
    }
    function le(j, V, ve, Se) {
      if (x._opts.validateSchema !== !1) {
        var be = j.definition.dependencies;
        if (be && !be.every(function(Me) {
          return Object.prototype.hasOwnProperty.call(ve, Me);
        }))
          throw new Error("parent schema must have all required keywords: " + be.join(","));
        var Ne = j.definition.validateSchema;
        if (Ne) {
          var Te = Ne(V);
          if (!Te) {
            var Ae = "keyword schema is invalid: " + x.errorsText(Ne.errors);
            if (x._opts.validateSchema == "log") x.logger.error(Ae);
            else throw new Error(Ae);
          }
        }
      }
      var Ye = j.definition.compile, Fe = j.definition.inline, wt = j.definition.macro, rt;
      if (Ye)
        rt = Ye.call(x, V, ve, Se);
      else if (wt)
        rt = wt.call(x, V, ve, Se), T.validateSchema !== !1 && x.validateSchema(rt, !0);
      else if (Fe)
        rt = Fe.call(x, Se, j.keyword, V, ve);
      else if (rt = j.definition.validate, !rt) return;
      if (rt === void 0)
        throw new Error('custom keyword "' + j.keyword + '"failed to compile');
      var Le = L.length;
      return L[Le] = rt, {
        code: "customRule" + Le,
        validate: rt
      };
    }
  }
  function l(P, I, A) {
    var b = m.call(this, P, I, A);
    return b >= 0 ? { index: b, compiling: !0 } : (b = this._compilations.length, this._compilations[b] = {
      schema: P,
      root: I,
      baseId: A
    }, { index: b, compiling: !1 });
  }
  function h(P, I, A) {
    var b = m.call(this, P, I, A);
    b >= 0 && this._compilations.splice(b, 1);
  }
  function m(P, I, A) {
    for (var b = 0; b < this._compilations.length; b++) {
      var x = this._compilations[b];
      if (x.schema == P && x.root == I && x.baseId == A) return b;
    }
    return -1;
  }
  function _(P, I) {
    return "var pattern" + P + " = new RegExp(" + e.toQuotedString(I[P]) + ");";
  }
  function c(P) {
    return "var default" + P + " = defaults[" + P + "];";
  }
  function f(P, I) {
    return I[P] === void 0 ? "" : "var refVal" + P + " = refVal[" + P + "];";
  }
  function y(P) {
    return "var customRule" + P + " = customRules[" + P + "];";
  }
  function v(P, I) {
    if (!P.length) return "";
    for (var A = "", b = 0; b < P.length; b++)
      A += I(b, P);
    return A;
  }
  return Oa;
}
var Ca = { exports: {} }, ei;
function td() {
  if (ei) return Ca.exports;
  ei = 1;
  var s = Ca.exports = function() {
    this._cache = {};
  };
  return s.prototype.put = function(r, a) {
    this._cache[r] = a;
  }, s.prototype.get = function(r) {
    return this._cache[r];
  }, s.prototype.del = function(r) {
    delete this._cache[r];
  }, s.prototype.clear = function() {
    this._cache = {};
  }, Ca.exports;
}
var $a, ti;
function rd() {
  if (ti) return $a;
  ti = 1;
  var s = yr(), e = /^(\d\d\d\d)-(\d\d)-(\d\d)$/, r = [0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31], a = /^(\d\d):(\d\d):(\d\d)(\.\d+)?(z|[+-]\d\d(?::?\d\d)?)?$/i, t = /^(?=.{1,253}\.?$)[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?(?:\.[a-z0-9](?:[-0-9a-z]{0,61}[0-9a-z])?)*\.?$/i, n = /^(?:[a-z][a-z0-9+\-.]*:)(?:\/?\/(?:(?:[a-z0-9\-._~!$&'()*+,;=:]|%[0-9a-f]{2})*@)?(?:\[(?:(?:(?:(?:[0-9a-f]{1,4}:){6}|::(?:[0-9a-f]{1,4}:){5}|(?:[0-9a-f]{1,4})?::(?:[0-9a-f]{1,4}:){4}|(?:(?:[0-9a-f]{1,4}:){0,1}[0-9a-f]{1,4})?::(?:[0-9a-f]{1,4}:){3}|(?:(?:[0-9a-f]{1,4}:){0,2}[0-9a-f]{1,4})?::(?:[0-9a-f]{1,4}:){2}|(?:(?:[0-9a-f]{1,4}:){0,3}[0-9a-f]{1,4})?::[0-9a-f]{1,4}:|(?:(?:[0-9a-f]{1,4}:){0,4}[0-9a-f]{1,4})?::)(?:[0-9a-f]{1,4}:[0-9a-f]{1,4}|(?:(?:25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(?:25[0-5]|2[0-4]\d|[01]?\d\d?))|(?:(?:[0-9a-f]{1,4}:){0,5}[0-9a-f]{1,4})?::[0-9a-f]{1,4}|(?:(?:[0-9a-f]{1,4}:){0,6}[0-9a-f]{1,4})?::)|[Vv][0-9a-f]+\.[a-z0-9\-._~!$&'()*+,;=:]+)\]|(?:(?:25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(?:25[0-5]|2[0-4]\d|[01]?\d\d?)|(?:[a-z0-9\-._~!$&'()*+,;=]|%[0-9a-f]{2})*)(?::\d*)?(?:\/(?:[a-z0-9\-._~!$&'()*+,;=:@]|%[0-9a-f]{2})*)*|\/(?:(?:[a-z0-9\-._~!$&'()*+,;=:@]|%[0-9a-f]{2})+(?:\/(?:[a-z0-9\-._~!$&'()*+,;=:@]|%[0-9a-f]{2})*)*)?|(?:[a-z0-9\-._~!$&'()*+,;=:@]|%[0-9a-f]{2})+(?:\/(?:[a-z0-9\-._~!$&'()*+,;=:@]|%[0-9a-f]{2})*)*)(?:\?(?:[a-z0-9\-._~!$&'()*+,;=:@/?]|%[0-9a-f]{2})*)?(?:#(?:[a-z0-9\-._~!$&'()*+,;=:@/?]|%[0-9a-f]{2})*)?$/i, i = /^(?:[a-z][a-z0-9+\-.]*:)?(?:\/?\/(?:(?:[a-z0-9\-._~!$&'()*+,;=:]|%[0-9a-f]{2})*@)?(?:\[(?:(?:(?:(?:[0-9a-f]{1,4}:){6}|::(?:[0-9a-f]{1,4}:){5}|(?:[0-9a-f]{1,4})?::(?:[0-9a-f]{1,4}:){4}|(?:(?:[0-9a-f]{1,4}:){0,1}[0-9a-f]{1,4})?::(?:[0-9a-f]{1,4}:){3}|(?:(?:[0-9a-f]{1,4}:){0,2}[0-9a-f]{1,4})?::(?:[0-9a-f]{1,4}:){2}|(?:(?:[0-9a-f]{1,4}:){0,3}[0-9a-f]{1,4})?::[0-9a-f]{1,4}:|(?:(?:[0-9a-f]{1,4}:){0,4}[0-9a-f]{1,4})?::)(?:[0-9a-f]{1,4}:[0-9a-f]{1,4}|(?:(?:25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(?:25[0-5]|2[0-4]\d|[01]?\d\d?))|(?:(?:[0-9a-f]{1,4}:){0,5}[0-9a-f]{1,4})?::[0-9a-f]{1,4}|(?:(?:[0-9a-f]{1,4}:){0,6}[0-9a-f]{1,4})?::)|[Vv][0-9a-f]+\.[a-z0-9\-._~!$&'()*+,;=:]+)\]|(?:(?:25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(?:25[0-5]|2[0-4]\d|[01]?\d\d?)|(?:[a-z0-9\-._~!$&'"()*+,;=]|%[0-9a-f]{2})*)(?::\d*)?(?:\/(?:[a-z0-9\-._~!$&'"()*+,;=:@]|%[0-9a-f]{2})*)*|\/(?:(?:[a-z0-9\-._~!$&'"()*+,;=:@]|%[0-9a-f]{2})+(?:\/(?:[a-z0-9\-._~!$&'"()*+,;=:@]|%[0-9a-f]{2})*)*)?|(?:[a-z0-9\-._~!$&'"()*+,;=:@]|%[0-9a-f]{2})+(?:\/(?:[a-z0-9\-._~!$&'"()*+,;=:@]|%[0-9a-f]{2})*)*)?(?:\?(?:[a-z0-9\-._~!$&'"()*+,;=:@/?]|%[0-9a-f]{2})*)?(?:#(?:[a-z0-9\-._~!$&'"()*+,;=:@/?]|%[0-9a-f]{2})*)?$/i, o = /^(?:(?:[^\x00-\x20"'<>%\\^`{|}]|%[0-9a-f]{2})|\{[+#./;?&=,!@|]?(?:[a-z0-9_]|%[0-9a-f]{2})+(?::[1-9][0-9]{0,3}|\*)?(?:,(?:[a-z0-9_]|%[0-9a-f]{2})+(?::[1-9][0-9]{0,3}|\*)?)*\})*$/i, u = /^(?:(?:http[s\u017F]?|ftp):\/\/)(?:(?:[\0-\x08\x0E-\x1F!-\x9F\xA1-\u167F\u1681-\u1FFF\u200B-\u2027\u202A-\u202E\u2030-\u205E\u2060-\u2FFF\u3001-\uD7FF\uE000-\uFEFE\uFF00-\uFFFF]|[\uD800-\uDBFF][\uDC00-\uDFFF]|[\uD800-\uDBFF](?![\uDC00-\uDFFF])|(?:[^\uD800-\uDBFF]|^)[\uDC00-\uDFFF])+(?::(?:[\0-\x08\x0E-\x1F!-\x9F\xA1-\u167F\u1681-\u1FFF\u200B-\u2027\u202A-\u202E\u2030-\u205E\u2060-\u2FFF\u3001-\uD7FF\uE000-\uFEFE\uFF00-\uFFFF]|[\uD800-\uDBFF][\uDC00-\uDFFF]|[\uD800-\uDBFF](?![\uDC00-\uDFFF])|(?:[^\uD800-\uDBFF]|^)[\uDC00-\uDFFF])*)?@)?(?:(?!10(?:\.[0-9]{1,3}){3})(?!127(?:\.[0-9]{1,3}){3})(?!169\.254(?:\.[0-9]{1,3}){2})(?!192\.168(?:\.[0-9]{1,3}){2})(?!172\.(?:1[6-9]|2[0-9]|3[01])(?:\.[0-9]{1,3}){2})(?:[1-9][0-9]?|1[0-9][0-9]|2[01][0-9]|22[0-3])(?:\.(?:1?[0-9]{1,2}|2[0-4][0-9]|25[0-5])){2}(?:\.(?:[1-9][0-9]?|1[0-9][0-9]|2[0-4][0-9]|25[0-4]))|(?:(?:(?:[0-9a-z\xA1-\uD7FF\uE000-\uFFFF]|[\uD800-\uDBFF](?![\uDC00-\uDFFF])|(?:[^\uD800-\uDBFF]|^)[\uDC00-\uDFFF])+-)*(?:[0-9a-z\xA1-\uD7FF\uE000-\uFFFF]|[\uD800-\uDBFF](?![\uDC00-\uDFFF])|(?:[^\uD800-\uDBFF]|^)[\uDC00-\uDFFF])+)(?:\.(?:(?:[0-9a-z\xA1-\uD7FF\uE000-\uFFFF]|[\uD800-\uDBFF](?![\uDC00-\uDFFF])|(?:[^\uD800-\uDBFF]|^)[\uDC00-\uDFFF])+-)*(?:[0-9a-z\xA1-\uD7FF\uE000-\uFFFF]|[\uD800-\uDBFF](?![\uDC00-\uDFFF])|(?:[^\uD800-\uDBFF]|^)[\uDC00-\uDFFF])+)*(?:\.(?:(?:[a-z\xA1-\uD7FF\uE000-\uFFFF]|[\uD800-\uDBFF](?![\uDC00-\uDFFF])|(?:[^\uD800-\uDBFF]|^)[\uDC00-\uDFFF]){2,})))(?::[0-9]{2,5})?(?:\/(?:[\0-\x08\x0E-\x1F!-\x9F\xA1-\u167F\u1681-\u1FFF\u200B-\u2027\u202A-\u202E\u2030-\u205E\u2060-\u2FFF\u3001-\uD7FF\uE000-\uFEFE\uFF00-\uFFFF]|[\uD800-\uDBFF][\uDC00-\uDFFF]|[\uD800-\uDBFF](?![\uDC00-\uDFFF])|(?:[^\uD800-\uDBFF]|^)[\uDC00-\uDFFF])*)?$/i, l = /^(?:urn:uuid:)?[0-9a-f]{8}-(?:[0-9a-f]{4}-){3}[0-9a-f]{12}$/i, h = /^(?:\/(?:[^~/]|~0|~1)*)*$/, m = /^#(?:\/(?:[a-z0-9_\-.!$&'()*+,;:=@]|%[0-9a-f]{2}|~0|~1)*)*$/i, _ = /^(?:0|[1-9][0-9]*)(?:#|(?:\/(?:[^~/]|~0|~1)*)*)$/;
  $a = c;
  function c(O) {
    return O = O == "full" ? "full" : "fast", s.copy(c[O]);
  }
  c.fast = {
    // date: http://tools.ietf.org/html/rfc3339#section-5.6
    date: /^\d\d\d\d-[0-1]\d-[0-3]\d$/,
    // date-time: http://tools.ietf.org/html/rfc3339#section-5.6
    time: /^(?:[0-2]\d:[0-5]\d:[0-5]\d|23:59:60)(?:\.\d+)?(?:z|[+-]\d\d(?::?\d\d)?)?$/i,
    "date-time": /^\d\d\d\d-[0-1]\d-[0-3]\d[t\s](?:[0-2]\d:[0-5]\d:[0-5]\d|23:59:60)(?:\.\d+)?(?:z|[+-]\d\d(?::?\d\d)?)$/i,
    // uri: https://github.com/mafintosh/is-my-json-valid/blob/master/formats.js
    uri: /^(?:[a-z][a-z0-9+\-.]*:)(?:\/?\/)?[^\s]*$/i,
    "uri-reference": /^(?:(?:[a-z][a-z0-9+\-.]*:)?\/?\/)?(?:[^\\\s#][^\s#]*)?(?:#[^\\\s]*)?$/i,
    "uri-template": o,
    url: u,
    // email (sources from jsen validator):
    // http://stackoverflow.com/questions/201323/using-a-regular-expression-to-validate-an-email-address#answer-8829363
    // http://www.w3.org/TR/html5/forms.html#valid-e-mail-address (search for 'willful violation')
    email: /^[a-z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?(?:\.[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?)*$/i,
    hostname: t,
    // optimized https://www.safaribooksonline.com/library/view/regular-expressions-cookbook/9780596802837/ch07s16.html
    ipv4: /^(?:(?:25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(?:25[0-5]|2[0-4]\d|[01]?\d\d?)$/,
    // optimized http://stackoverflow.com/questions/53497/regular-expression-that-matches-valid-ipv6-addresses
    ipv6: /^\s*(?:(?:(?:[0-9a-f]{1,4}:){7}(?:[0-9a-f]{1,4}|:))|(?:(?:[0-9a-f]{1,4}:){6}(?::[0-9a-f]{1,4}|(?:(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(?:\.(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(?:(?:[0-9a-f]{1,4}:){5}(?:(?:(?::[0-9a-f]{1,4}){1,2})|:(?:(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(?:\.(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(?:(?:[0-9a-f]{1,4}:){4}(?:(?:(?::[0-9a-f]{1,4}){1,3})|(?:(?::[0-9a-f]{1,4})?:(?:(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(?:\.(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(?:(?:[0-9a-f]{1,4}:){3}(?:(?:(?::[0-9a-f]{1,4}){1,4})|(?:(?::[0-9a-f]{1,4}){0,2}:(?:(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(?:\.(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(?:(?:[0-9a-f]{1,4}:){2}(?:(?:(?::[0-9a-f]{1,4}){1,5})|(?:(?::[0-9a-f]{1,4}){0,3}:(?:(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(?:\.(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(?:(?:[0-9a-f]{1,4}:){1}(?:(?:(?::[0-9a-f]{1,4}){1,6})|(?:(?::[0-9a-f]{1,4}){0,4}:(?:(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(?:\.(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(?::(?:(?:(?::[0-9a-f]{1,4}){1,7})|(?:(?::[0-9a-f]{1,4}){0,5}:(?:(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(?:\.(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:)))(?:%.+)?\s*$/i,
    regex: T,
    // uuid: http://tools.ietf.org/html/rfc4122
    uuid: l,
    // JSON-pointer: https://tools.ietf.org/html/rfc6901
    // uri fragment: https://tools.ietf.org/html/rfc3986#appendix-A
    "json-pointer": h,
    "json-pointer-uri-fragment": m,
    // relative JSON-pointer: http://tools.ietf.org/html/draft-luff-relative-json-pointer-00
    "relative-json-pointer": _
  }, c.full = {
    date: y,
    time: v,
    "date-time": I,
    uri: b,
    "uri-reference": i,
    "uri-template": o,
    url: u,
    email: /^[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/i,
    hostname: t,
    ipv4: /^(?:(?:25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(?:25[0-5]|2[0-4]\d|[01]?\d\d?)$/,
    ipv6: /^\s*(?:(?:(?:[0-9a-f]{1,4}:){7}(?:[0-9a-f]{1,4}|:))|(?:(?:[0-9a-f]{1,4}:){6}(?::[0-9a-f]{1,4}|(?:(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(?:\.(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(?:(?:[0-9a-f]{1,4}:){5}(?:(?:(?::[0-9a-f]{1,4}){1,2})|:(?:(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(?:\.(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(?:(?:[0-9a-f]{1,4}:){4}(?:(?:(?::[0-9a-f]{1,4}){1,3})|(?:(?::[0-9a-f]{1,4})?:(?:(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(?:\.(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(?:(?:[0-9a-f]{1,4}:){3}(?:(?:(?::[0-9a-f]{1,4}){1,4})|(?:(?::[0-9a-f]{1,4}){0,2}:(?:(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(?:\.(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(?:(?:[0-9a-f]{1,4}:){2}(?:(?:(?::[0-9a-f]{1,4}){1,5})|(?:(?::[0-9a-f]{1,4}){0,3}:(?:(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(?:\.(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(?:(?:[0-9a-f]{1,4}:){1}(?:(?:(?::[0-9a-f]{1,4}){1,6})|(?:(?::[0-9a-f]{1,4}){0,4}:(?:(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(?:\.(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(?::(?:(?:(?::[0-9a-f]{1,4}){1,7})|(?:(?::[0-9a-f]{1,4}){0,5}:(?:(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(?:\.(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:)))(?:%.+)?\s*$/i,
    regex: T,
    uuid: l,
    "json-pointer": h,
    "json-pointer-uri-fragment": m,
    "relative-json-pointer": _
  };
  function f(O) {
    return O % 4 === 0 && (O % 100 !== 0 || O % 400 === 0);
  }
  function y(O) {
    var C = O.match(e);
    if (!C) return !1;
    var $ = +C[1], S = +C[2], E = +C[3];
    return S >= 1 && S <= 12 && E >= 1 && E <= (S == 2 && f($) ? 29 : r[S]);
  }
  function v(O, C) {
    var $ = O.match(a);
    if (!$) return !1;
    var S = $[1], E = $[2], N = $[3], L = $[5];
    return (S <= 23 && E <= 59 && N <= 59 || S == 23 && E == 59 && N == 60) && (!C || L);
  }
  var P = /t|\s/i;
  function I(O) {
    var C = O.split(P);
    return C.length == 2 && y(C[0]) && v(C[1], !0);
  }
  var A = /\/|:/;
  function b(O) {
    return A.test(O) && n.test(O);
  }
  var x = /[^\\]\\Z/;
  function T(O) {
    if (x.test(O)) return !1;
    try {
      return new RegExp(O), !0;
    } catch {
      return !1;
    }
  }
  return $a;
}
var Ia, ri;
function ad() {
  return ri || (ri = 1, Ia = function(e, r, a) {
    var t = " ", n = e.level, i = e.dataLevel, o = e.schema[r], u = e.errSchemaPath + "/" + r, l = !e.opts.allErrors, h = "data" + (i || ""), m = "valid" + n, _, c;
    if (o == "#" || o == "#/")
      e.isRoot ? (_ = e.async, c = "validate") : (_ = e.root.schema.$async === !0, c = "root.refVal[0]");
    else {
      var f = e.resolveRef(e.baseId, o, e.isRoot);
      if (f === void 0) {
        var y = e.MissingRefError.message(e.baseId, o);
        if (e.opts.missingRefs == "fail") {
          e.logger.error(y);
          var v = v || [];
          v.push(t), t = "", e.createErrors !== !1 ? (t += " { keyword: '$ref' , dataPath: (dataPath || '') + " + e.errorPath + " , schemaPath: " + e.util.toQuotedString(u) + " , params: { ref: '" + e.util.escapeQuotes(o) + "' } ", e.opts.messages !== !1 && (t += " , message: 'can\\'t resolve reference " + e.util.escapeQuotes(o) + "' "), e.opts.verbose && (t += " , schema: " + e.util.toQuotedString(o) + " , parentSchema: validate.schema" + e.schemaPath + " , data: " + h + " "), t += " } ") : t += " {} ";
          var P = t;
          t = v.pop(), !e.compositeRule && l ? e.async ? t += " throw new ValidationError([" + P + "]); " : t += " validate.errors = [" + P + "]; return false; " : t += " var err = " + P + ";  if (vErrors === null) vErrors = [err]; else vErrors.push(err); errors++; ", l && (t += " if (false) { ");
        } else if (e.opts.missingRefs == "ignore")
          e.logger.warn(y), l && (t += " if (true) { ");
        else
          throw new e.MissingRefError(e.baseId, o, y);
      } else if (f.inline) {
        var I = e.util.copy(e);
        I.level++;
        var A = "valid" + I.level;
        I.schema = f.schema, I.schemaPath = "", I.errSchemaPath = o;
        var b = e.validate(I).replace(/validate\.schema/g, f.code);
        t += " " + b + " ", l && (t += " if (" + A + ") { ");
      } else
        _ = f.$async === !0 || e.async && f.$async !== !1, c = f.code;
    }
    if (c) {
      var v = v || [];
      v.push(t), t = "", e.opts.passContext ? t += " " + c + ".call(this, " : t += " " + c + "( ", t += " " + h + ", (dataPath || '')", e.errorPath != '""' && (t += " + " + e.errorPath);
      var x = i ? "data" + (i - 1 || "") : "parentData", T = i ? e.dataPathArr[i] : "parentDataProperty";
      t += " , " + x + " , " + T + ", rootData)  ";
      var O = t;
      if (t = v.pop(), _) {
        if (!e.async) throw new Error("async schema referenced by sync schema");
        l && (t += " var " + m + "; "), t += " try { await " + O + "; ", l && (t += " " + m + " = true; "), t += " } catch (e) { if (!(e instanceof ValidationError)) throw e; if (vErrors === null) vErrors = e.errors; else vErrors = vErrors.concat(e.errors); errors = vErrors.length; ", l && (t += " " + m + " = false; "), t += " } ", l && (t += " if (" + m + ") { ");
      } else
        t += " if (!" + O + ") { if (vErrors === null) vErrors = " + c + ".errors; else vErrors = vErrors.concat(" + c + ".errors); errors = vErrors.length; } ", l && (t += " else { ");
    }
    return t;
  }), Ia;
}
var Na, ai;
function sd() {
  return ai || (ai = 1, Na = function(e, r, a) {
    var t = " ", n = e.schema[r], i = e.schemaPath + e.util.getProperty(r), o = e.errSchemaPath + "/" + r, u = !e.opts.allErrors, l = e.util.copy(e), h = "";
    l.level++;
    var m = "valid" + l.level, _ = l.baseId, c = !0, f = n;
    if (f)
      for (var y, v = -1, P = f.length - 1; v < P; )
        y = f[v += 1], (e.opts.strictKeywords ? typeof y == "object" && Object.keys(y).length > 0 || y === !1 : e.util.schemaHasRules(y, e.RULES.all)) && (c = !1, l.schema = y, l.schemaPath = i + "[" + v + "]", l.errSchemaPath = o + "/" + v, t += "  " + e.validate(l) + " ", l.baseId = _, u && (t += " if (" + m + ") { ", h += "}"));
    return u && (c ? t += " if (true) { " : t += " " + h.slice(0, -1) + " "), t;
  }), Na;
}
var Da, si;
function nd() {
  return si || (si = 1, Da = function(e, r, a) {
    var t = " ", n = e.level, i = e.dataLevel, o = e.schema[r], u = e.schemaPath + e.util.getProperty(r), l = e.errSchemaPath + "/" + r, h = !e.opts.allErrors, m = "data" + (i || ""), _ = "valid" + n, c = "errs__" + n, f = e.util.copy(e), y = "";
    f.level++;
    var v = "valid" + f.level, P = o.every(function(C) {
      return e.opts.strictKeywords ? typeof C == "object" && Object.keys(C).length > 0 || C === !1 : e.util.schemaHasRules(C, e.RULES.all);
    });
    if (P) {
      var I = f.baseId;
      t += " var " + c + " = errors; var " + _ + " = false;  ";
      var A = e.compositeRule;
      e.compositeRule = f.compositeRule = !0;
      var b = o;
      if (b)
        for (var x, T = -1, O = b.length - 1; T < O; )
          x = b[T += 1], f.schema = x, f.schemaPath = u + "[" + T + "]", f.errSchemaPath = l + "/" + T, t += "  " + e.validate(f) + " ", f.baseId = I, t += " " + _ + " = " + _ + " || " + v + "; if (!" + _ + ") { ", y += "}";
      e.compositeRule = f.compositeRule = A, t += " " + y + " if (!" + _ + ") {   var err =   ", e.createErrors !== !1 ? (t += " { keyword: 'anyOf' , dataPath: (dataPath || '') + " + e.errorPath + " , schemaPath: " + e.util.toQuotedString(l) + " , params: {} ", e.opts.messages !== !1 && (t += " , message: 'should match some schema in anyOf' "), e.opts.verbose && (t += " , schema: validate.schema" + u + " , parentSchema: validate.schema" + e.schemaPath + " , data: " + m + " "), t += " } ") : t += " {} ", t += ";  if (vErrors === null) vErrors = [err]; else vErrors.push(err); errors++; ", !e.compositeRule && h && (e.async ? t += " throw new ValidationError(vErrors); " : t += " validate.errors = vErrors; return false; "), t += " } else {  errors = " + c + "; if (vErrors !== null) { if (" + c + ") vErrors.length = " + c + "; else vErrors = null; } ", e.opts.allErrors && (t += " } ");
    } else
      h && (t += " if (true) { ");
    return t;
  }), Da;
}
var ja, ni;
function id() {
  return ni || (ni = 1, ja = function(e, r, a) {
    var t = " ", n = e.schema[r], i = e.errSchemaPath + "/" + r;
    e.opts.allErrors;
    var o = e.util.toQuotedString(n);
    return e.opts.$comment === !0 ? t += " console.log(" + o + ");" : typeof e.opts.$comment == "function" && (t += " self._opts.$comment(" + o + ", " + e.util.toQuotedString(i) + ", validate.root.schema);"), t;
  }), ja;
}
var Fa, ii;
function od() {
  return ii || (ii = 1, Fa = function(e, r, a) {
    var t = " ", n = e.level, i = e.dataLevel, o = e.schema[r], u = e.schemaPath + e.util.getProperty(r), l = e.errSchemaPath + "/" + r, h = !e.opts.allErrors, m = "data" + (i || ""), _ = "valid" + n, c = e.opts.$data && o && o.$data;
    c && (t += " var schema" + n + " = " + e.util.getData(o.$data, i, e.dataPathArr) + "; "), c || (t += " var schema" + n + " = validate.schema" + u + ";"), t += "var " + _ + " = equal(" + m + ", schema" + n + "); if (!" + _ + ") {   ";
    var f = f || [];
    f.push(t), t = "", e.createErrors !== !1 ? (t += " { keyword: 'const' , dataPath: (dataPath || '') + " + e.errorPath + " , schemaPath: " + e.util.toQuotedString(l) + " , params: { allowedValue: schema" + n + " } ", e.opts.messages !== !1 && (t += " , message: 'should be equal to constant' "), e.opts.verbose && (t += " , schema: validate.schema" + u + " , parentSchema: validate.schema" + e.schemaPath + " , data: " + m + " "), t += " } ") : t += " {} ";
    var y = t;
    return t = f.pop(), !e.compositeRule && h ? e.async ? t += " throw new ValidationError([" + y + "]); " : t += " validate.errors = [" + y + "]; return false; " : t += " var err = " + y + ";  if (vErrors === null) vErrors = [err]; else vErrors.push(err); errors++; ", t += " }", h && (t += " else { "), t;
  }), Fa;
}
var qa, oi;
function ud() {
  return oi || (oi = 1, qa = function(e, r, a) {
    var t = " ", n = e.level, i = e.dataLevel, o = e.schema[r], u = e.schemaPath + e.util.getProperty(r), l = e.errSchemaPath + "/" + r, h = !e.opts.allErrors, m = "data" + (i || ""), _ = "valid" + n, c = "errs__" + n, f = e.util.copy(e), y = "";
    f.level++;
    var v = "valid" + f.level, P = "i" + n, I = f.dataLevel = e.dataLevel + 1, A = "data" + I, b = e.baseId, x = e.opts.strictKeywords ? typeof o == "object" && Object.keys(o).length > 0 || o === !1 : e.util.schemaHasRules(o, e.RULES.all);
    if (t += "var " + c + " = errors;var " + _ + ";", x) {
      var T = e.compositeRule;
      e.compositeRule = f.compositeRule = !0, f.schema = o, f.schemaPath = u, f.errSchemaPath = l, t += " var " + v + " = false; for (var " + P + " = 0; " + P + " < " + m + ".length; " + P + "++) { ", f.errorPath = e.util.getPathExpr(e.errorPath, P, e.opts.jsonPointers, !0);
      var O = m + "[" + P + "]";
      f.dataPathArr[I] = P;
      var C = e.validate(f);
      f.baseId = b, e.util.varOccurences(C, A) < 2 ? t += " " + e.util.varReplace(C, A, O) + " " : t += " var " + A + " = " + O + "; " + C + " ", t += " if (" + v + ") break; }  ", e.compositeRule = f.compositeRule = T, t += " " + y + " if (!" + v + ") {";
    } else
      t += " if (" + m + ".length == 0) {";
    var $ = $ || [];
    $.push(t), t = "", e.createErrors !== !1 ? (t += " { keyword: 'contains' , dataPath: (dataPath || '') + " + e.errorPath + " , schemaPath: " + e.util.toQuotedString(l) + " , params: {} ", e.opts.messages !== !1 && (t += " , message: 'should contain a valid item' "), e.opts.verbose && (t += " , schema: validate.schema" + u + " , parentSchema: validate.schema" + e.schemaPath + " , data: " + m + " "), t += " } ") : t += " {} ";
    var S = t;
    return t = $.pop(), !e.compositeRule && h ? e.async ? t += " throw new ValidationError([" + S + "]); " : t += " validate.errors = [" + S + "]; return false; " : t += " var err = " + S + ";  if (vErrors === null) vErrors = [err]; else vErrors.push(err); errors++; ", t += " } else { ", x && (t += "  errors = " + c + "; if (vErrors !== null) { if (" + c + ") vErrors.length = " + c + "; else vErrors = null; } "), e.opts.allErrors && (t += " } "), t;
  }), qa;
}
var La, ui;
function ld() {
  return ui || (ui = 1, La = function(e, r, a) {
    var t = " ", n = e.level, i = e.dataLevel, o = e.schema[r], u = e.schemaPath + e.util.getProperty(r), l = e.errSchemaPath + "/" + r, h = !e.opts.allErrors, m = "data" + (i || ""), _ = "errs__" + n, c = e.util.copy(e), f = "";
    c.level++;
    var y = "valid" + c.level, v = {}, P = {}, I = e.opts.ownProperties;
    for (T in o)
      if (T != "__proto__") {
        var A = o[T], b = Array.isArray(A) ? P : v;
        b[T] = A;
      }
    t += "var " + _ + " = errors;";
    var x = e.errorPath;
    t += "var missing" + n + ";";
    for (var T in P)
      if (b = P[T], b.length) {
        if (t += " if ( " + m + e.util.getProperty(T) + " !== undefined ", I && (t += " && Object.prototype.hasOwnProperty.call(" + m + ", '" + e.util.escapeQuotes(T) + "') "), h) {
          t += " && ( ";
          var O = b;
          if (O)
            for (var C, $ = -1, S = O.length - 1; $ < S; ) {
              C = O[$ += 1], $ && (t += " || ");
              var E = e.util.getProperty(C), N = m + E;
              t += " ( ( " + N + " === undefined ", I && (t += " || ! Object.prototype.hasOwnProperty.call(" + m + ", '" + e.util.escapeQuotes(C) + "') "), t += ") && (missing" + n + " = " + e.util.toQuotedString(e.opts.jsonPointers ? C : E) + ") ) ";
            }
          t += ")) {  ";
          var L = "missing" + n, Z = "' + " + L + " + '";
          e.opts._errorDataPathProperty && (e.errorPath = e.opts.jsonPointers ? e.util.getPathExpr(x, L, !0) : x + " + " + L);
          var re = re || [];
          re.push(t), t = "", e.createErrors !== !1 ? (t += " { keyword: 'dependencies' , dataPath: (dataPath || '') + " + e.errorPath + " , schemaPath: " + e.util.toQuotedString(l) + " , params: { property: '" + e.util.escapeQuotes(T) + "', missingProperty: '" + Z + "', depsCount: " + b.length + ", deps: '" + e.util.escapeQuotes(b.length == 1 ? b[0] : b.join(", ")) + "' } ", e.opts.messages !== !1 && (t += " , message: 'should have ", b.length == 1 ? t += "property " + e.util.escapeQuotes(b[0]) : t += "properties " + e.util.escapeQuotes(b.join(", ")), t += " when property " + e.util.escapeQuotes(T) + " is present' "), e.opts.verbose && (t += " , schema: validate.schema" + u + " , parentSchema: validate.schema" + e.schemaPath + " , data: " + m + " "), t += " } ") : t += " {} ";
          var fe = t;
          t = re.pop(), !e.compositeRule && h ? e.async ? t += " throw new ValidationError([" + fe + "]); " : t += " validate.errors = [" + fe + "]; return false; " : t += " var err = " + fe + ";  if (vErrors === null) vErrors = [err]; else vErrors.push(err); errors++; ";
        } else {
          t += " ) { ";
          var ne = b;
          if (ne)
            for (var C, _e = -1, ce = ne.length - 1; _e < ce; ) {
              C = ne[_e += 1];
              var E = e.util.getProperty(C), Z = e.util.escapeQuotes(C), N = m + E;
              e.opts._errorDataPathProperty && (e.errorPath = e.util.getPath(x, C, e.opts.jsonPointers)), t += " if ( " + N + " === undefined ", I && (t += " || ! Object.prototype.hasOwnProperty.call(" + m + ", '" + e.util.escapeQuotes(C) + "') "), t += ") {  var err =   ", e.createErrors !== !1 ? (t += " { keyword: 'dependencies' , dataPath: (dataPath || '') + " + e.errorPath + " , schemaPath: " + e.util.toQuotedString(l) + " , params: { property: '" + e.util.escapeQuotes(T) + "', missingProperty: '" + Z + "', depsCount: " + b.length + ", deps: '" + e.util.escapeQuotes(b.length == 1 ? b[0] : b.join(", ")) + "' } ", e.opts.messages !== !1 && (t += " , message: 'should have ", b.length == 1 ? t += "property " + e.util.escapeQuotes(b[0]) : t += "properties " + e.util.escapeQuotes(b.join(", ")), t += " when property " + e.util.escapeQuotes(T) + " is present' "), e.opts.verbose && (t += " , schema: validate.schema" + u + " , parentSchema: validate.schema" + e.schemaPath + " , data: " + m + " "), t += " } ") : t += " {} ", t += ";  if (vErrors === null) vErrors = [err]; else vErrors.push(err); errors++; } ";
            }
        }
        t += " }   ", h && (f += "}", t += " else { ");
      }
    e.errorPath = x;
    var de = c.baseId;
    for (var T in v) {
      var A = v[T];
      (e.opts.strictKeywords ? typeof A == "object" && Object.keys(A).length > 0 || A === !1 : e.util.schemaHasRules(A, e.RULES.all)) && (t += " " + y + " = true; if ( " + m + e.util.getProperty(T) + " !== undefined ", I && (t += " && Object.prototype.hasOwnProperty.call(" + m + ", '" + e.util.escapeQuotes(T) + "') "), t += ") { ", c.schema = A, c.schemaPath = u + e.util.getProperty(T), c.errSchemaPath = l + "/" + e.util.escapeFragment(T), t += "  " + e.validate(c) + " ", c.baseId = de, t += " }  ", h && (t += " if (" + y + ") { ", f += "}"));
    }
    return h && (t += "   " + f + " if (" + _ + " == errors) {"), t;
  }), La;
}
var Za, li;
function cd() {
  return li || (li = 1, Za = function(e, r, a) {
    var t = " ", n = e.level, i = e.dataLevel, o = e.schema[r], u = e.schemaPath + e.util.getProperty(r), l = e.errSchemaPath + "/" + r, h = !e.opts.allErrors, m = "data" + (i || ""), _ = "valid" + n, c = e.opts.$data && o && o.$data;
    c && (t += " var schema" + n + " = " + e.util.getData(o.$data, i, e.dataPathArr) + "; ");
    var f = "i" + n, y = "schema" + n;
    c || (t += " var " + y + " = validate.schema" + u + ";"), t += "var " + _ + ";", c && (t += " if (schema" + n + " === undefined) " + _ + " = true; else if (!Array.isArray(schema" + n + ")) " + _ + " = false; else {"), t += "" + _ + " = false;for (var " + f + "=0; " + f + "<" + y + ".length; " + f + "++) if (equal(" + m + ", " + y + "[" + f + "])) { " + _ + " = true; break; }", c && (t += "  }  "), t += " if (!" + _ + ") {   ";
    var v = v || [];
    v.push(t), t = "", e.createErrors !== !1 ? (t += " { keyword: 'enum' , dataPath: (dataPath || '') + " + e.errorPath + " , schemaPath: " + e.util.toQuotedString(l) + " , params: { allowedValues: schema" + n + " } ", e.opts.messages !== !1 && (t += " , message: 'should be equal to one of the allowed values' "), e.opts.verbose && (t += " , schema: validate.schema" + u + " , parentSchema: validate.schema" + e.schemaPath + " , data: " + m + " "), t += " } ") : t += " {} ";
    var P = t;
    return t = v.pop(), !e.compositeRule && h ? e.async ? t += " throw new ValidationError([" + P + "]); " : t += " validate.errors = [" + P + "]; return false; " : t += " var err = " + P + ";  if (vErrors === null) vErrors = [err]; else vErrors.push(err); errors++; ", t += " }", h && (t += " else { "), t;
  }), Za;
}
var Ma, ci;
function dd() {
  return ci || (ci = 1, Ma = function(e, r, a) {
    var t = " ", n = e.level, i = e.dataLevel, o = e.schema[r], u = e.schemaPath + e.util.getProperty(r), l = e.errSchemaPath + "/" + r, h = !e.opts.allErrors, m = "data" + (i || "");
    if (e.opts.format === !1)
      return h && (t += " if (true) { "), t;
    var _ = e.opts.$data && o && o.$data, c;
    _ ? (t += " var schema" + n + " = " + e.util.getData(o.$data, i, e.dataPathArr) + "; ", c = "schema" + n) : c = o;
    var f = e.opts.unknownFormats, y = Array.isArray(f);
    if (_) {
      var v = "format" + n, P = "isObject" + n, I = "formatType" + n;
      t += " var " + v + " = formats[" + c + "]; var " + P + " = typeof " + v + " == 'object' && !(" + v + " instanceof RegExp) && " + v + ".validate; var " + I + " = " + P + " && " + v + ".type || 'string'; if (" + P + ") { ", e.async && (t += " var async" + n + " = " + v + ".async; "), t += " " + v + " = " + v + ".validate; } if (  ", _ && (t += " (" + c + " !== undefined && typeof " + c + " != 'string') || "), t += " (", f != "ignore" && (t += " (" + c + " && !" + v + " ", y && (t += " && self._opts.unknownFormats.indexOf(" + c + ") == -1 "), t += ") || "), t += " (" + v + " && " + I + " == '" + a + "' && !(typeof " + v + " == 'function' ? ", e.async ? t += " (async" + n + " ? await " + v + "(" + m + ") : " + v + "(" + m + ")) " : t += " " + v + "(" + m + ") ", t += " : " + v + ".test(" + m + "))))) {";
    } else {
      var v = e.formats[o];
      if (!v) {
        if (f == "ignore")
          return e.logger.warn('unknown format "' + o + '" ignored in schema at path "' + e.errSchemaPath + '"'), h && (t += " if (true) { "), t;
        if (y && f.indexOf(o) >= 0)
          return h && (t += " if (true) { "), t;
        throw new Error('unknown format "' + o + '" is used in schema at path "' + e.errSchemaPath + '"');
      }
      var P = typeof v == "object" && !(v instanceof RegExp) && v.validate, I = P && v.type || "string";
      if (P) {
        var A = v.async === !0;
        v = v.validate;
      }
      if (I != a)
        return h && (t += " if (true) { "), t;
      if (A) {
        if (!e.async) throw new Error("async format in sync schema");
        var b = "formats" + e.util.getProperty(o) + ".validate";
        t += " if (!(await " + b + "(" + m + "))) { ";
      } else {
        t += " if (! ";
        var b = "formats" + e.util.getProperty(o);
        P && (b += ".validate"), typeof v == "function" ? t += " " + b + "(" + m + ") " : t += " " + b + ".test(" + m + ") ", t += ") { ";
      }
    }
    var x = x || [];
    x.push(t), t = "", e.createErrors !== !1 ? (t += " { keyword: 'format' , dataPath: (dataPath || '') + " + e.errorPath + " , schemaPath: " + e.util.toQuotedString(l) + " , params: { format:  ", _ ? t += "" + c : t += "" + e.util.toQuotedString(o), t += "  } ", e.opts.messages !== !1 && (t += ` , message: 'should match format "`, _ ? t += "' + " + c + " + '" : t += "" + e.util.escapeQuotes(o), t += `"' `), e.opts.verbose && (t += " , schema:  ", _ ? t += "validate.schema" + u : t += "" + e.util.toQuotedString(o), t += "         , parentSchema: validate.schema" + e.schemaPath + " , data: " + m + " "), t += " } ") : t += " {} ";
    var T = t;
    return t = x.pop(), !e.compositeRule && h ? e.async ? t += " throw new ValidationError([" + T + "]); " : t += " validate.errors = [" + T + "]; return false; " : t += " var err = " + T + ";  if (vErrors === null) vErrors = [err]; else vErrors.push(err); errors++; ", t += " } ", h && (t += " else { "), t;
  }), Ma;
}
var za, di;
function hd() {
  return di || (di = 1, za = function(e, r, a) {
    var t = " ", n = e.level, i = e.dataLevel, o = e.schema[r], u = e.schemaPath + e.util.getProperty(r), l = e.errSchemaPath + "/" + r, h = !e.opts.allErrors, m = "data" + (i || ""), _ = "valid" + n, c = "errs__" + n, f = e.util.copy(e);
    f.level++;
    var y = "valid" + f.level, v = e.schema.then, P = e.schema.else, I = v !== void 0 && (e.opts.strictKeywords ? typeof v == "object" && Object.keys(v).length > 0 || v === !1 : e.util.schemaHasRules(v, e.RULES.all)), A = P !== void 0 && (e.opts.strictKeywords ? typeof P == "object" && Object.keys(P).length > 0 || P === !1 : e.util.schemaHasRules(P, e.RULES.all)), b = f.baseId;
    if (I || A) {
      var x;
      f.createErrors = !1, f.schema = o, f.schemaPath = u, f.errSchemaPath = l, t += " var " + c + " = errors; var " + _ + " = true;  ";
      var T = e.compositeRule;
      e.compositeRule = f.compositeRule = !0, t += "  " + e.validate(f) + " ", f.baseId = b, f.createErrors = !0, t += "  errors = " + c + "; if (vErrors !== null) { if (" + c + ") vErrors.length = " + c + "; else vErrors = null; }  ", e.compositeRule = f.compositeRule = T, I ? (t += " if (" + y + ") {  ", f.schema = e.schema.then, f.schemaPath = e.schemaPath + ".then", f.errSchemaPath = e.errSchemaPath + "/then", t += "  " + e.validate(f) + " ", f.baseId = b, t += " " + _ + " = " + y + "; ", I && A ? (x = "ifClause" + n, t += " var " + x + " = 'then'; ") : x = "'then'", t += " } ", A && (t += " else { ")) : t += " if (!" + y + ") { ", A && (f.schema = e.schema.else, f.schemaPath = e.schemaPath + ".else", f.errSchemaPath = e.errSchemaPath + "/else", t += "  " + e.validate(f) + " ", f.baseId = b, t += " " + _ + " = " + y + "; ", I && A ? (x = "ifClause" + n, t += " var " + x + " = 'else'; ") : x = "'else'", t += " } "), t += " if (!" + _ + ") {   var err =   ", e.createErrors !== !1 ? (t += " { keyword: 'if' , dataPath: (dataPath || '') + " + e.errorPath + " , schemaPath: " + e.util.toQuotedString(l) + " , params: { failingKeyword: " + x + " } ", e.opts.messages !== !1 && (t += ` , message: 'should match "' + ` + x + ` + '" schema' `), e.opts.verbose && (t += " , schema: validate.schema" + u + " , parentSchema: validate.schema" + e.schemaPath + " , data: " + m + " "), t += " } ") : t += " {} ", t += ";  if (vErrors === null) vErrors = [err]; else vErrors.push(err); errors++; ", !e.compositeRule && h && (e.async ? t += " throw new ValidationError(vErrors); " : t += " validate.errors = vErrors; return false; "), t += " }   ", h && (t += " else { ");
    } else
      h && (t += " if (true) { ");
    return t;
  }), za;
}
var Ua, hi;
function fd() {
  return hi || (hi = 1, Ua = function(e, r, a) {
    var t = " ", n = e.level, i = e.dataLevel, o = e.schema[r], u = e.schemaPath + e.util.getProperty(r), l = e.errSchemaPath + "/" + r, h = !e.opts.allErrors, m = "data" + (i || ""), _ = "valid" + n, c = "errs__" + n, f = e.util.copy(e), y = "";
    f.level++;
    var v = "valid" + f.level, P = "i" + n, I = f.dataLevel = e.dataLevel + 1, A = "data" + I, b = e.baseId;
    if (t += "var " + c + " = errors;var " + _ + ";", Array.isArray(o)) {
      var x = e.schema.additionalItems;
      if (x === !1) {
        t += " " + _ + " = " + m + ".length <= " + o.length + "; ";
        var T = l;
        l = e.errSchemaPath + "/additionalItems", t += "  if (!" + _ + ") {   ";
        var O = O || [];
        O.push(t), t = "", e.createErrors !== !1 ? (t += " { keyword: 'additionalItems' , dataPath: (dataPath || '') + " + e.errorPath + " , schemaPath: " + e.util.toQuotedString(l) + " , params: { limit: " + o.length + " } ", e.opts.messages !== !1 && (t += " , message: 'should NOT have more than " + o.length + " items' "), e.opts.verbose && (t += " , schema: false , parentSchema: validate.schema" + e.schemaPath + " , data: " + m + " "), t += " } ") : t += " {} ";
        var C = t;
        t = O.pop(), !e.compositeRule && h ? e.async ? t += " throw new ValidationError([" + C + "]); " : t += " validate.errors = [" + C + "]; return false; " : t += " var err = " + C + ";  if (vErrors === null) vErrors = [err]; else vErrors.push(err); errors++; ", t += " } ", l = T, h && (y += "}", t += " else { ");
      }
      var $ = o;
      if ($) {
        for (var S, E = -1, N = $.length - 1; E < N; )
          if (S = $[E += 1], e.opts.strictKeywords ? typeof S == "object" && Object.keys(S).length > 0 || S === !1 : e.util.schemaHasRules(S, e.RULES.all)) {
            t += " " + v + " = true; if (" + m + ".length > " + E + ") { ";
            var L = m + "[" + E + "]";
            f.schema = S, f.schemaPath = u + "[" + E + "]", f.errSchemaPath = l + "/" + E, f.errorPath = e.util.getPathExpr(e.errorPath, E, e.opts.jsonPointers, !0), f.dataPathArr[I] = E;
            var Z = e.validate(f);
            f.baseId = b, e.util.varOccurences(Z, A) < 2 ? t += " " + e.util.varReplace(Z, A, L) + " " : t += " var " + A + " = " + L + "; " + Z + " ", t += " }  ", h && (t += " if (" + v + ") { ", y += "}");
          }
      }
      if (typeof x == "object" && (e.opts.strictKeywords ? typeof x == "object" && Object.keys(x).length > 0 || x === !1 : e.util.schemaHasRules(x, e.RULES.all))) {
        f.schema = x, f.schemaPath = e.schemaPath + ".additionalItems", f.errSchemaPath = e.errSchemaPath + "/additionalItems", t += " " + v + " = true; if (" + m + ".length > " + o.length + ") {  for (var " + P + " = " + o.length + "; " + P + " < " + m + ".length; " + P + "++) { ", f.errorPath = e.util.getPathExpr(e.errorPath, P, e.opts.jsonPointers, !0);
        var L = m + "[" + P + "]";
        f.dataPathArr[I] = P;
        var Z = e.validate(f);
        f.baseId = b, e.util.varOccurences(Z, A) < 2 ? t += " " + e.util.varReplace(Z, A, L) + " " : t += " var " + A + " = " + L + "; " + Z + " ", h && (t += " if (!" + v + ") break; "), t += " } }  ", h && (t += " if (" + v + ") { ", y += "}");
      }
    } else if (e.opts.strictKeywords ? typeof o == "object" && Object.keys(o).length > 0 || o === !1 : e.util.schemaHasRules(o, e.RULES.all)) {
      f.schema = o, f.schemaPath = u, f.errSchemaPath = l, t += "  for (var " + P + " = 0; " + P + " < " + m + ".length; " + P + "++) { ", f.errorPath = e.util.getPathExpr(e.errorPath, P, e.opts.jsonPointers, !0);
      var L = m + "[" + P + "]";
      f.dataPathArr[I] = P;
      var Z = e.validate(f);
      f.baseId = b, e.util.varOccurences(Z, A) < 2 ? t += " " + e.util.varReplace(Z, A, L) + " " : t += " var " + A + " = " + L + "; " + Z + " ", h && (t += " if (!" + v + ") break; "), t += " }";
    }
    return h && (t += " " + y + " if (" + c + " == errors) {"), t;
  }), Ua;
}
var Va, fi;
function pi() {
  return fi || (fi = 1, Va = function(e, r, a) {
    var t = " ", n = e.level, i = e.dataLevel, o = e.schema[r], u = e.schemaPath + e.util.getProperty(r), l = e.errSchemaPath + "/" + r, h = !e.opts.allErrors, b, m = "data" + (i || ""), _ = e.opts.$data && o && o.$data, c;
    _ ? (t += " var schema" + n + " = " + e.util.getData(o.$data, i, e.dataPathArr) + "; ", c = "schema" + n) : c = o;
    var f = r == "maximum", y = f ? "exclusiveMaximum" : "exclusiveMinimum", v = e.schema[y], P = e.opts.$data && v && v.$data, I = f ? "<" : ">", A = f ? ">" : "<", b = void 0;
    if (!(_ || typeof o == "number" || o === void 0))
      throw new Error(r + " must be number");
    if (!(P || v === void 0 || typeof v == "number" || typeof v == "boolean"))
      throw new Error(y + " must be number or boolean");
    if (P) {
      var x = e.util.getData(v.$data, i, e.dataPathArr), T = "exclusive" + n, O = "exclType" + n, C = "exclIsNumber" + n, $ = "op" + n, S = "' + " + $ + " + '";
      t += " var schemaExcl" + n + " = " + x + "; ", x = "schemaExcl" + n, t += " var " + T + "; var " + O + " = typeof " + x + "; if (" + O + " != 'boolean' && " + O + " != 'undefined' && " + O + " != 'number') { ";
      var b = y, E = E || [];
      E.push(t), t = "", e.createErrors !== !1 ? (t += " { keyword: '" + (b || "_exclusiveLimit") + "' , dataPath: (dataPath || '') + " + e.errorPath + " , schemaPath: " + e.util.toQuotedString(l) + " , params: {} ", e.opts.messages !== !1 && (t += " , message: '" + y + " should be boolean' "), e.opts.verbose && (t += " , schema: validate.schema" + u + " , parentSchema: validate.schema" + e.schemaPath + " , data: " + m + " "), t += " } ") : t += " {} ";
      var N = t;
      t = E.pop(), !e.compositeRule && h ? e.async ? t += " throw new ValidationError([" + N + "]); " : t += " validate.errors = [" + N + "]; return false; " : t += " var err = " + N + ";  if (vErrors === null) vErrors = [err]; else vErrors.push(err); errors++; ", t += " } else if ( ", _ && (t += " (" + c + " !== undefined && typeof " + c + " != 'number') || "), t += " " + O + " == 'number' ? ( (" + T + " = " + c + " === undefined || " + x + " " + I + "= " + c + ") ? " + m + " " + A + "= " + x + " : " + m + " " + A + " " + c + " ) : ( (" + T + " = " + x + " === true) ? " + m + " " + A + "= " + c + " : " + m + " " + A + " " + c + " ) || " + m + " !== " + m + ") { var op" + n + " = " + T + " ? '" + I + "' : '" + I + "='; ", o === void 0 && (b = y, l = e.errSchemaPath + "/" + y, c = x, _ = P);
    } else {
      var C = typeof v == "number", S = I;
      if (C && _) {
        var $ = "'" + S + "'";
        t += " if ( ", _ && (t += " (" + c + " !== undefined && typeof " + c + " != 'number') || "), t += " ( " + c + " === undefined || " + v + " " + I + "= " + c + " ? " + m + " " + A + "= " + v + " : " + m + " " + A + " " + c + " ) || " + m + " !== " + m + ") { ";
      } else {
        C && o === void 0 ? (T = !0, b = y, l = e.errSchemaPath + "/" + y, c = v, A += "=") : (C && (c = Math[f ? "min" : "max"](v, o)), v === (C ? c : !0) ? (T = !0, b = y, l = e.errSchemaPath + "/" + y, A += "=") : (T = !1, S += "="));
        var $ = "'" + S + "'";
        t += " if ( ", _ && (t += " (" + c + " !== undefined && typeof " + c + " != 'number') || "), t += " " + m + " " + A + " " + c + " || " + m + " !== " + m + ") { ";
      }
    }
    b = b || r;
    var E = E || [];
    E.push(t), t = "", e.createErrors !== !1 ? (t += " { keyword: '" + (b || "_limit") + "' , dataPath: (dataPath || '') + " + e.errorPath + " , schemaPath: " + e.util.toQuotedString(l) + " , params: { comparison: " + $ + ", limit: " + c + ", exclusive: " + T + " } ", e.opts.messages !== !1 && (t += " , message: 'should be " + S + " ", _ ? t += "' + " + c : t += "" + c + "'"), e.opts.verbose && (t += " , schema:  ", _ ? t += "validate.schema" + u : t += "" + o, t += "         , parentSchema: validate.schema" + e.schemaPath + " , data: " + m + " "), t += " } ") : t += " {} ";
    var N = t;
    return t = E.pop(), !e.compositeRule && h ? e.async ? t += " throw new ValidationError([" + N + "]); " : t += " validate.errors = [" + N + "]; return false; " : t += " var err = " + N + ";  if (vErrors === null) vErrors = [err]; else vErrors.push(err); errors++; ", t += " } ", h && (t += " else { "), t;
  }), Va;
}
var Ha, mi;
function vi() {
  return mi || (mi = 1, Ha = function(e, r, a) {
    var t = " ", n = e.level, i = e.dataLevel, o = e.schema[r], u = e.schemaPath + e.util.getProperty(r), l = e.errSchemaPath + "/" + r, h = !e.opts.allErrors, y, m = "data" + (i || ""), _ = e.opts.$data && o && o.$data, c;
    if (_ ? (t += " var schema" + n + " = " + e.util.getData(o.$data, i, e.dataPathArr) + "; ", c = "schema" + n) : c = o, !(_ || typeof o == "number"))
      throw new Error(r + " must be number");
    var f = r == "maxItems" ? ">" : "<";
    t += "if ( ", _ && (t += " (" + c + " !== undefined && typeof " + c + " != 'number') || "), t += " " + m + ".length " + f + " " + c + ") { ";
    var y = r, v = v || [];
    v.push(t), t = "", e.createErrors !== !1 ? (t += " { keyword: '" + (y || "_limitItems") + "' , dataPath: (dataPath || '') + " + e.errorPath + " , schemaPath: " + e.util.toQuotedString(l) + " , params: { limit: " + c + " } ", e.opts.messages !== !1 && (t += " , message: 'should NOT have ", r == "maxItems" ? t += "more" : t += "fewer", t += " than ", _ ? t += "' + " + c + " + '" : t += "" + o, t += " items' "), e.opts.verbose && (t += " , schema:  ", _ ? t += "validate.schema" + u : t += "" + o, t += "         , parentSchema: validate.schema" + e.schemaPath + " , data: " + m + " "), t += " } ") : t += " {} ";
    var P = t;
    return t = v.pop(), !e.compositeRule && h ? e.async ? t += " throw new ValidationError([" + P + "]); " : t += " validate.errors = [" + P + "]; return false; " : t += " var err = " + P + ";  if (vErrors === null) vErrors = [err]; else vErrors.push(err); errors++; ", t += "} ", h && (t += " else { "), t;
  }), Ha;
}
var Ba, gi;
function yi() {
  return gi || (gi = 1, Ba = function(e, r, a) {
    var t = " ", n = e.level, i = e.dataLevel, o = e.schema[r], u = e.schemaPath + e.util.getProperty(r), l = e.errSchemaPath + "/" + r, h = !e.opts.allErrors, y, m = "data" + (i || ""), _ = e.opts.$data && o && o.$data, c;
    if (_ ? (t += " var schema" + n + " = " + e.util.getData(o.$data, i, e.dataPathArr) + "; ", c = "schema" + n) : c = o, !(_ || typeof o == "number"))
      throw new Error(r + " must be number");
    var f = r == "maxLength" ? ">" : "<";
    t += "if ( ", _ && (t += " (" + c + " !== undefined && typeof " + c + " != 'number') || "), e.opts.unicode === !1 ? t += " " + m + ".length " : t += " ucs2length(" + m + ") ", t += " " + f + " " + c + ") { ";
    var y = r, v = v || [];
    v.push(t), t = "", e.createErrors !== !1 ? (t += " { keyword: '" + (y || "_limitLength") + "' , dataPath: (dataPath || '') + " + e.errorPath + " , schemaPath: " + e.util.toQuotedString(l) + " , params: { limit: " + c + " } ", e.opts.messages !== !1 && (t += " , message: 'should NOT be ", r == "maxLength" ? t += "longer" : t += "shorter", t += " than ", _ ? t += "' + " + c + " + '" : t += "" + o, t += " characters' "), e.opts.verbose && (t += " , schema:  ", _ ? t += "validate.schema" + u : t += "" + o, t += "         , parentSchema: validate.schema" + e.schemaPath + " , data: " + m + " "), t += " } ") : t += " {} ";
    var P = t;
    return t = v.pop(), !e.compositeRule && h ? e.async ? t += " throw new ValidationError([" + P + "]); " : t += " validate.errors = [" + P + "]; return false; " : t += " var err = " + P + ";  if (vErrors === null) vErrors = [err]; else vErrors.push(err); errors++; ", t += "} ", h && (t += " else { "), t;
  }), Ba;
}
var Qa, _i;
function bi() {
  return _i || (_i = 1, Qa = function(e, r, a) {
    var t = " ", n = e.level, i = e.dataLevel, o = e.schema[r], u = e.schemaPath + e.util.getProperty(r), l = e.errSchemaPath + "/" + r, h = !e.opts.allErrors, y, m = "data" + (i || ""), _ = e.opts.$data && o && o.$data, c;
    if (_ ? (t += " var schema" + n + " = " + e.util.getData(o.$data, i, e.dataPathArr) + "; ", c = "schema" + n) : c = o, !(_ || typeof o == "number"))
      throw new Error(r + " must be number");
    var f = r == "maxProperties" ? ">" : "<";
    t += "if ( ", _ && (t += " (" + c + " !== undefined && typeof " + c + " != 'number') || "), t += " Object.keys(" + m + ").length " + f + " " + c + ") { ";
    var y = r, v = v || [];
    v.push(t), t = "", e.createErrors !== !1 ? (t += " { keyword: '" + (y || "_limitProperties") + "' , dataPath: (dataPath || '') + " + e.errorPath + " , schemaPath: " + e.util.toQuotedString(l) + " , params: { limit: " + c + " } ", e.opts.messages !== !1 && (t += " , message: 'should NOT have ", r == "maxProperties" ? t += "more" : t += "fewer", t += " than ", _ ? t += "' + " + c + " + '" : t += "" + o, t += " properties' "), e.opts.verbose && (t += " , schema:  ", _ ? t += "validate.schema" + u : t += "" + o, t += "         , parentSchema: validate.schema" + e.schemaPath + " , data: " + m + " "), t += " } ") : t += " {} ";
    var P = t;
    return t = v.pop(), !e.compositeRule && h ? e.async ? t += " throw new ValidationError([" + P + "]); " : t += " validate.errors = [" + P + "]; return false; " : t += " var err = " + P + ";  if (vErrors === null) vErrors = [err]; else vErrors.push(err); errors++; ", t += "} ", h && (t += " else { "), t;
  }), Qa;
}
var Ka, Pi;
function pd() {
  return Pi || (Pi = 1, Ka = function(e, r, a) {
    var t = " ", n = e.level, i = e.dataLevel, o = e.schema[r], u = e.schemaPath + e.util.getProperty(r), l = e.errSchemaPath + "/" + r, h = !e.opts.allErrors, m = "data" + (i || ""), _ = e.opts.$data && o && o.$data, c;
    if (_ ? (t += " var schema" + n + " = " + e.util.getData(o.$data, i, e.dataPathArr) + "; ", c = "schema" + n) : c = o, !(_ || typeof o == "number"))
      throw new Error(r + " must be number");
    t += "var division" + n + ";if (", _ && (t += " " + c + " !== undefined && ( typeof " + c + " != 'number' || "), t += " (division" + n + " = " + m + " / " + c + ", ", e.opts.multipleOfPrecision ? t += " Math.abs(Math.round(division" + n + ") - division" + n + ") > 1e-" + e.opts.multipleOfPrecision + " " : t += " division" + n + " !== parseInt(division" + n + ") ", t += " ) ", _ && (t += "  )  "), t += " ) {   ";
    var f = f || [];
    f.push(t), t = "", e.createErrors !== !1 ? (t += " { keyword: 'multipleOf' , dataPath: (dataPath || '') + " + e.errorPath + " , schemaPath: " + e.util.toQuotedString(l) + " , params: { multipleOf: " + c + " } ", e.opts.messages !== !1 && (t += " , message: 'should be multiple of ", _ ? t += "' + " + c : t += "" + c + "'"), e.opts.verbose && (t += " , schema:  ", _ ? t += "validate.schema" + u : t += "" + o, t += "         , parentSchema: validate.schema" + e.schemaPath + " , data: " + m + " "), t += " } ") : t += " {} ";
    var y = t;
    return t = f.pop(), !e.compositeRule && h ? e.async ? t += " throw new ValidationError([" + y + "]); " : t += " validate.errors = [" + y + "]; return false; " : t += " var err = " + y + ";  if (vErrors === null) vErrors = [err]; else vErrors.push(err); errors++; ", t += "} ", h && (t += " else { "), t;
  }), Ka;
}
var Ja, Si;
function md() {
  return Si || (Si = 1, Ja = function(e, r, a) {
    var t = " ", n = e.level, i = e.dataLevel, o = e.schema[r], u = e.schemaPath + e.util.getProperty(r), l = e.errSchemaPath + "/" + r, h = !e.opts.allErrors, m = "data" + (i || ""), _ = "errs__" + n, c = e.util.copy(e);
    c.level++;
    var f = "valid" + c.level;
    if (e.opts.strictKeywords ? typeof o == "object" && Object.keys(o).length > 0 || o === !1 : e.util.schemaHasRules(o, e.RULES.all)) {
      c.schema = o, c.schemaPath = u, c.errSchemaPath = l, t += " var " + _ + " = errors;  ";
      var y = e.compositeRule;
      e.compositeRule = c.compositeRule = !0, c.createErrors = !1;
      var v;
      c.opts.allErrors && (v = c.opts.allErrors, c.opts.allErrors = !1), t += " " + e.validate(c) + " ", c.createErrors = !0, v && (c.opts.allErrors = v), e.compositeRule = c.compositeRule = y, t += " if (" + f + ") {   ";
      var P = P || [];
      P.push(t), t = "", e.createErrors !== !1 ? (t += " { keyword: 'not' , dataPath: (dataPath || '') + " + e.errorPath + " , schemaPath: " + e.util.toQuotedString(l) + " , params: {} ", e.opts.messages !== !1 && (t += " , message: 'should NOT be valid' "), e.opts.verbose && (t += " , schema: validate.schema" + u + " , parentSchema: validate.schema" + e.schemaPath + " , data: " + m + " "), t += " } ") : t += " {} ";
      var I = t;
      t = P.pop(), !e.compositeRule && h ? e.async ? t += " throw new ValidationError([" + I + "]); " : t += " validate.errors = [" + I + "]; return false; " : t += " var err = " + I + ";  if (vErrors === null) vErrors = [err]; else vErrors.push(err); errors++; ", t += " } else {  errors = " + _ + "; if (vErrors !== null) { if (" + _ + ") vErrors.length = " + _ + "; else vErrors = null; } ", e.opts.allErrors && (t += " } ");
    } else
      t += "  var err =   ", e.createErrors !== !1 ? (t += " { keyword: 'not' , dataPath: (dataPath || '') + " + e.errorPath + " , schemaPath: " + e.util.toQuotedString(l) + " , params: {} ", e.opts.messages !== !1 && (t += " , message: 'should NOT be valid' "), e.opts.verbose && (t += " , schema: validate.schema" + u + " , parentSchema: validate.schema" + e.schemaPath + " , data: " + m + " "), t += " } ") : t += " {} ", t += ";  if (vErrors === null) vErrors = [err]; else vErrors.push(err); errors++; ", h && (t += " if (false) { ");
    return t;
  }), Ja;
}
var Wa, wi;
function vd() {
  return wi || (wi = 1, Wa = function(e, r, a) {
    var t = " ", n = e.level, i = e.dataLevel, o = e.schema[r], u = e.schemaPath + e.util.getProperty(r), l = e.errSchemaPath + "/" + r, h = !e.opts.allErrors, m = "data" + (i || ""), _ = "valid" + n, c = "errs__" + n, f = e.util.copy(e), y = "";
    f.level++;
    var v = "valid" + f.level, P = f.baseId, I = "prevValid" + n, A = "passingSchemas" + n;
    t += "var " + c + " = errors , " + I + " = false , " + _ + " = false , " + A + " = null; ";
    var b = e.compositeRule;
    e.compositeRule = f.compositeRule = !0;
    var x = o;
    if (x)
      for (var T, O = -1, C = x.length - 1; O < C; )
        T = x[O += 1], (e.opts.strictKeywords ? typeof T == "object" && Object.keys(T).length > 0 || T === !1 : e.util.schemaHasRules(T, e.RULES.all)) ? (f.schema = T, f.schemaPath = u + "[" + O + "]", f.errSchemaPath = l + "/" + O, t += "  " + e.validate(f) + " ", f.baseId = P) : t += " var " + v + " = true; ", O && (t += " if (" + v + " && " + I + ") { " + _ + " = false; " + A + " = [" + A + ", " + O + "]; } else { ", y += "}"), t += " if (" + v + ") { " + _ + " = " + I + " = true; " + A + " = " + O + "; }";
    return e.compositeRule = f.compositeRule = b, t += "" + y + "if (!" + _ + ") {   var err =   ", e.createErrors !== !1 ? (t += " { keyword: 'oneOf' , dataPath: (dataPath || '') + " + e.errorPath + " , schemaPath: " + e.util.toQuotedString(l) + " , params: { passingSchemas: " + A + " } ", e.opts.messages !== !1 && (t += " , message: 'should match exactly one schema in oneOf' "), e.opts.verbose && (t += " , schema: validate.schema" + u + " , parentSchema: validate.schema" + e.schemaPath + " , data: " + m + " "), t += " } ") : t += " {} ", t += ";  if (vErrors === null) vErrors = [err]; else vErrors.push(err); errors++; ", !e.compositeRule && h && (e.async ? t += " throw new ValidationError(vErrors); " : t += " validate.errors = vErrors; return false; "), t += "} else {  errors = " + c + "; if (vErrors !== null) { if (" + c + ") vErrors.length = " + c + "; else vErrors = null; }", e.opts.allErrors && (t += " } "), t;
  }), Wa;
}
var Ga, xi;
function gd() {
  return xi || (xi = 1, Ga = function(e, r, a) {
    var t = " ", n = e.level, i = e.dataLevel, o = e.schema[r], u = e.schemaPath + e.util.getProperty(r), l = e.errSchemaPath + "/" + r, h = !e.opts.allErrors, m = "data" + (i || ""), _ = e.opts.$data && o && o.$data, c;
    _ ? (t += " var schema" + n + " = " + e.util.getData(o.$data, i, e.dataPathArr) + "; ", c = "schema" + n) : c = o;
    var f = _ ? "(new RegExp(" + c + "))" : e.usePattern(o);
    t += "if ( ", _ && (t += " (" + c + " !== undefined && typeof " + c + " != 'string') || "), t += " !" + f + ".test(" + m + ") ) {   ";
    var y = y || [];
    y.push(t), t = "", e.createErrors !== !1 ? (t += " { keyword: 'pattern' , dataPath: (dataPath || '') + " + e.errorPath + " , schemaPath: " + e.util.toQuotedString(l) + " , params: { pattern:  ", _ ? t += "" + c : t += "" + e.util.toQuotedString(o), t += "  } ", e.opts.messages !== !1 && (t += ` , message: 'should match pattern "`, _ ? t += "' + " + c + " + '" : t += "" + e.util.escapeQuotes(o), t += `"' `), e.opts.verbose && (t += " , schema:  ", _ ? t += "validate.schema" + u : t += "" + e.util.toQuotedString(o), t += "         , parentSchema: validate.schema" + e.schemaPath + " , data: " + m + " "), t += " } ") : t += " {} ";
    var v = t;
    return t = y.pop(), !e.compositeRule && h ? e.async ? t += " throw new ValidationError([" + v + "]); " : t += " validate.errors = [" + v + "]; return false; " : t += " var err = " + v + ";  if (vErrors === null) vErrors = [err]; else vErrors.push(err); errors++; ", t += "} ", h && (t += " else { "), t;
  }), Ga;
}
var Ya, Ei;
function yd() {
  return Ei || (Ei = 1, Ya = function(e, r, a) {
    var t = " ", n = e.level, i = e.dataLevel, o = e.schema[r], u = e.schemaPath + e.util.getProperty(r), l = e.errSchemaPath + "/" + r, h = !e.opts.allErrors, m = "data" + (i || ""), _ = "errs__" + n, c = e.util.copy(e), f = "";
    c.level++;
    var y = "valid" + c.level, v = "key" + n, P = "idx" + n, I = c.dataLevel = e.dataLevel + 1, A = "data" + I, b = "dataProperties" + n, x = Object.keys(o || {}).filter(_e), T = e.schema.patternProperties || {}, O = Object.keys(T).filter(_e), C = e.schema.additionalProperties, $ = x.length || O.length, S = C === !1, E = typeof C == "object" && Object.keys(C).length, N = e.opts.removeAdditional, L = S || E || N, Z = e.opts.ownProperties, re = e.baseId, fe = e.schema.required;
    if (fe && !(e.opts.$data && fe.$data) && fe.length < e.opts.loopRequired)
      var ne = e.util.toHash(fe);
    function _e(ga) {
      return ga !== "__proto__";
    }
    if (t += "var " + _ + " = errors;var " + y + " = true;", Z && (t += " var " + b + " = undefined;"), L) {
      if (Z ? t += " " + b + " = " + b + " || Object.keys(" + m + "); for (var " + P + "=0; " + P + "<" + b + ".length; " + P + "++) { var " + v + " = " + b + "[" + P + "]; " : t += " for (var " + v + " in " + m + ") { ", $) {
        if (t += " var isAdditional" + n + " = !(false ", x.length)
          if (x.length > 8)
            t += " || validate.schema" + u + ".hasOwnProperty(" + v + ") ";
          else {
            var ce = x;
            if (ce)
              for (var de, tt = -1, Ge = ce.length - 1; tt < Ge; )
                de = ce[tt += 1], t += " || " + v + " == " + e.util.toQuotedString(de) + " ";
          }
        if (O.length) {
          var Je = O;
          if (Je)
            for (var Ze, mt = -1, w = Je.length - 1; mt < w; )
              Ze = Je[mt += 1], t += " || " + e.usePattern(Ze) + ".test(" + v + ") ";
        }
        t += " ); if (isAdditional" + n + ") { ";
      }
      if (N == "all")
        t += " delete " + m + "[" + v + "]; ";
      else {
        var D = e.errorPath, B = "' + " + v + " + '";
        if (e.opts._errorDataPathProperty && (e.errorPath = e.util.getPathExpr(e.errorPath, v, e.opts.jsonPointers)), S)
          if (N)
            t += " delete " + m + "[" + v + "]; ";
          else {
            t += " " + y + " = false; ";
            var le = l;
            l = e.errSchemaPath + "/additionalProperties";
            var j = j || [];
            j.push(t), t = "", e.createErrors !== !1 ? (t += " { keyword: 'additionalProperties' , dataPath: (dataPath || '') + " + e.errorPath + " , schemaPath: " + e.util.toQuotedString(l) + " , params: { additionalProperty: '" + B + "' } ", e.opts.messages !== !1 && (t += " , message: '", e.opts._errorDataPathProperty ? t += "is an invalid additional property" : t += "should NOT have additional properties", t += "' "), e.opts.verbose && (t += " , schema: false , parentSchema: validate.schema" + e.schemaPath + " , data: " + m + " "), t += " } ") : t += " {} ";
            var V = t;
            t = j.pop(), !e.compositeRule && h ? e.async ? t += " throw new ValidationError([" + V + "]); " : t += " validate.errors = [" + V + "]; return false; " : t += " var err = " + V + ";  if (vErrors === null) vErrors = [err]; else vErrors.push(err); errors++; ", l = le, h && (t += " break; ");
          }
        else if (E)
          if (N == "failing") {
            t += " var " + _ + " = errors;  ";
            var ve = e.compositeRule;
            e.compositeRule = c.compositeRule = !0, c.schema = C, c.schemaPath = e.schemaPath + ".additionalProperties", c.errSchemaPath = e.errSchemaPath + "/additionalProperties", c.errorPath = e.opts._errorDataPathProperty ? e.errorPath : e.util.getPathExpr(e.errorPath, v, e.opts.jsonPointers);
            var Se = m + "[" + v + "]";
            c.dataPathArr[I] = v;
            var be = e.validate(c);
            c.baseId = re, e.util.varOccurences(be, A) < 2 ? t += " " + e.util.varReplace(be, A, Se) + " " : t += " var " + A + " = " + Se + "; " + be + " ", t += " if (!" + y + ") { errors = " + _ + "; if (validate.errors !== null) { if (errors) validate.errors.length = errors; else validate.errors = null; } delete " + m + "[" + v + "]; }  ", e.compositeRule = c.compositeRule = ve;
          } else {
            c.schema = C, c.schemaPath = e.schemaPath + ".additionalProperties", c.errSchemaPath = e.errSchemaPath + "/additionalProperties", c.errorPath = e.opts._errorDataPathProperty ? e.errorPath : e.util.getPathExpr(e.errorPath, v, e.opts.jsonPointers);
            var Se = m + "[" + v + "]";
            c.dataPathArr[I] = v;
            var be = e.validate(c);
            c.baseId = re, e.util.varOccurences(be, A) < 2 ? t += " " + e.util.varReplace(be, A, Se) + " " : t += " var " + A + " = " + Se + "; " + be + " ", h && (t += " if (!" + y + ") break; ");
          }
        e.errorPath = D;
      }
      $ && (t += " } "), t += " }  ", h && (t += " if (" + y + ") { ", f += "}");
    }
    var Ne = e.opts.useDefaults && !e.compositeRule;
    if (x.length) {
      var Te = x;
      if (Te)
        for (var de, Ae = -1, Ye = Te.length - 1; Ae < Ye; ) {
          de = Te[Ae += 1];
          var Fe = o[de];
          if (e.opts.strictKeywords ? typeof Fe == "object" && Object.keys(Fe).length > 0 || Fe === !1 : e.util.schemaHasRules(Fe, e.RULES.all)) {
            var wt = e.util.getProperty(de), Se = m + wt, rt = Ne && Fe.default !== void 0;
            c.schema = Fe, c.schemaPath = u + wt, c.errSchemaPath = l + "/" + e.util.escapeFragment(de), c.errorPath = e.util.getPath(e.errorPath, de, e.opts.jsonPointers), c.dataPathArr[I] = e.util.toQuotedString(de);
            var be = e.validate(c);
            if (c.baseId = re, e.util.varOccurences(be, A) < 2) {
              be = e.util.varReplace(be, A, Se);
              var Le = Se;
            } else {
              var Le = A;
              t += " var " + A + " = " + Se + "; ";
            }
            if (rt)
              t += " " + be + " ";
            else {
              if (ne && ne[de]) {
                t += " if ( " + Le + " === undefined ", Z && (t += " || ! Object.prototype.hasOwnProperty.call(" + m + ", '" + e.util.escapeQuotes(de) + "') "), t += ") { " + y + " = false; ";
                var D = e.errorPath, le = l, Me = e.util.escapeQuotes(de);
                e.opts._errorDataPathProperty && (e.errorPath = e.util.getPath(D, de, e.opts.jsonPointers)), l = e.errSchemaPath + "/required";
                var j = j || [];
                j.push(t), t = "", e.createErrors !== !1 ? (t += " { keyword: 'required' , dataPath: (dataPath || '') + " + e.errorPath + " , schemaPath: " + e.util.toQuotedString(l) + " , params: { missingProperty: '" + Me + "' } ", e.opts.messages !== !1 && (t += " , message: '", e.opts._errorDataPathProperty ? t += "is a required property" : t += "should have required property \\'" + Me + "\\'", t += "' "), e.opts.verbose && (t += " , schema: validate.schema" + u + " , parentSchema: validate.schema" + e.schemaPath + " , data: " + m + " "), t += " } ") : t += " {} ";
                var V = t;
                t = j.pop(), !e.compositeRule && h ? e.async ? t += " throw new ValidationError([" + V + "]); " : t += " validate.errors = [" + V + "]; return false; " : t += " var err = " + V + ";  if (vErrors === null) vErrors = [err]; else vErrors.push(err); errors++; ", l = le, e.errorPath = D, t += " } else { ";
              } else
                h ? (t += " if ( " + Le + " === undefined ", Z && (t += " || ! Object.prototype.hasOwnProperty.call(" + m + ", '" + e.util.escapeQuotes(de) + "') "), t += ") { " + y + " = true; } else { ") : (t += " if (" + Le + " !== undefined ", Z && (t += " &&   Object.prototype.hasOwnProperty.call(" + m + ", '" + e.util.escapeQuotes(de) + "') "), t += " ) { ");
              t += " " + be + " } ";
            }
          }
          h && (t += " if (" + y + ") { ", f += "}");
        }
    }
    if (O.length) {
      var lt = O;
      if (lt)
        for (var Ze, Fr = -1, va = lt.length - 1; Fr < va; ) {
          Ze = lt[Fr += 1];
          var Fe = T[Ze];
          if (e.opts.strictKeywords ? typeof Fe == "object" && Object.keys(Fe).length > 0 || Fe === !1 : e.util.schemaHasRules(Fe, e.RULES.all)) {
            c.schema = Fe, c.schemaPath = e.schemaPath + ".patternProperties" + e.util.getProperty(Ze), c.errSchemaPath = e.errSchemaPath + "/patternProperties/" + e.util.escapeFragment(Ze), Z ? t += " " + b + " = " + b + " || Object.keys(" + m + "); for (var " + P + "=0; " + P + "<" + b + ".length; " + P + "++) { var " + v + " = " + b + "[" + P + "]; " : t += " for (var " + v + " in " + m + ") { ", t += " if (" + e.usePattern(Ze) + ".test(" + v + ")) { ", c.errorPath = e.util.getPathExpr(e.errorPath, v, e.opts.jsonPointers);
            var Se = m + "[" + v + "]";
            c.dataPathArr[I] = v;
            var be = e.validate(c);
            c.baseId = re, e.util.varOccurences(be, A) < 2 ? t += " " + e.util.varReplace(be, A, Se) + " " : t += " var " + A + " = " + Se + "; " + be + " ", h && (t += " if (!" + y + ") break; "), t += " } ", h && (t += " else " + y + " = true; "), t += " }  ", h && (t += " if (" + y + ") { ", f += "}");
          }
        }
    }
    return h && (t += " " + f + " if (" + _ + " == errors) {"), t;
  }), Ya;
}
var Xa, Ri;
function _d() {
  return Ri || (Ri = 1, Xa = function(e, r, a) {
    var t = " ", n = e.level, i = e.dataLevel, o = e.schema[r], u = e.schemaPath + e.util.getProperty(r), l = e.errSchemaPath + "/" + r, h = !e.opts.allErrors, m = "data" + (i || ""), _ = "errs__" + n, c = e.util.copy(e), f = "";
    c.level++;
    var y = "valid" + c.level;
    if (t += "var " + _ + " = errors;", e.opts.strictKeywords ? typeof o == "object" && Object.keys(o).length > 0 || o === !1 : e.util.schemaHasRules(o, e.RULES.all)) {
      c.schema = o, c.schemaPath = u, c.errSchemaPath = l;
      var v = "key" + n, P = "idx" + n, I = "i" + n, A = "' + " + v + " + '", b = c.dataLevel = e.dataLevel + 1, x = "data" + b, T = "dataProperties" + n, O = e.opts.ownProperties, C = e.baseId;
      O && (t += " var " + T + " = undefined; "), O ? t += " " + T + " = " + T + " || Object.keys(" + m + "); for (var " + P + "=0; " + P + "<" + T + ".length; " + P + "++) { var " + v + " = " + T + "[" + P + "]; " : t += " for (var " + v + " in " + m + ") { ", t += " var startErrs" + n + " = errors; ";
      var $ = v, S = e.compositeRule;
      e.compositeRule = c.compositeRule = !0;
      var E = e.validate(c);
      c.baseId = C, e.util.varOccurences(E, x) < 2 ? t += " " + e.util.varReplace(E, x, $) + " " : t += " var " + x + " = " + $ + "; " + E + " ", e.compositeRule = c.compositeRule = S, t += " if (!" + y + ") { for (var " + I + "=startErrs" + n + "; " + I + "<errors; " + I + "++) { vErrors[" + I + "].propertyName = " + v + "; }   var err =   ", e.createErrors !== !1 ? (t += " { keyword: 'propertyNames' , dataPath: (dataPath || '') + " + e.errorPath + " , schemaPath: " + e.util.toQuotedString(l) + " , params: { propertyName: '" + A + "' } ", e.opts.messages !== !1 && (t += " , message: 'property name \\'" + A + "\\' is invalid' "), e.opts.verbose && (t += " , schema: validate.schema" + u + " , parentSchema: validate.schema" + e.schemaPath + " , data: " + m + " "), t += " } ") : t += " {} ", t += ";  if (vErrors === null) vErrors = [err]; else vErrors.push(err); errors++; ", !e.compositeRule && h && (e.async ? t += " throw new ValidationError(vErrors); " : t += " validate.errors = vErrors; return false; "), h && (t += " break; "), t += " } }";
    }
    return h && (t += " " + f + " if (" + _ + " == errors) {"), t;
  }), Xa;
}
var es, ki;
function bd() {
  return ki || (ki = 1, es = function(e, r, a) {
    var t = " ", n = e.level, i = e.dataLevel, o = e.schema[r], u = e.schemaPath + e.util.getProperty(r), l = e.errSchemaPath + "/" + r, h = !e.opts.allErrors, m = "data" + (i || ""), _ = "valid" + n, c = e.opts.$data && o && o.$data;
    c && (t += " var schema" + n + " = " + e.util.getData(o.$data, i, e.dataPathArr) + "; ");
    var f = "schema" + n;
    if (!c)
      if (o.length < e.opts.loopRequired && e.schema.properties && Object.keys(e.schema.properties).length) {
        var y = [], v = o;
        if (v)
          for (var P, I = -1, A = v.length - 1; I < A; ) {
            P = v[I += 1];
            var b = e.schema.properties[P];
            b && (e.opts.strictKeywords ? typeof b == "object" && Object.keys(b).length > 0 || b === !1 : e.util.schemaHasRules(b, e.RULES.all)) || (y[y.length] = P);
          }
      } else
        var y = o;
    if (c || y.length) {
      var x = e.errorPath, T = c || y.length >= e.opts.loopRequired, O = e.opts.ownProperties;
      if (h)
        if (t += " var missing" + n + "; ", T) {
          c || (t += " var " + f + " = validate.schema" + u + "; ");
          var C = "i" + n, $ = "schema" + n + "[" + C + "]", S = "' + " + $ + " + '";
          e.opts._errorDataPathProperty && (e.errorPath = e.util.getPathExpr(x, $, e.opts.jsonPointers)), t += " var " + _ + " = true; ", c && (t += " if (schema" + n + " === undefined) " + _ + " = true; else if (!Array.isArray(schema" + n + ")) " + _ + " = false; else {"), t += " for (var " + C + " = 0; " + C + " < " + f + ".length; " + C + "++) { " + _ + " = " + m + "[" + f + "[" + C + "]] !== undefined ", O && (t += " &&   Object.prototype.hasOwnProperty.call(" + m + ", " + f + "[" + C + "]) "), t += "; if (!" + _ + ") break; } ", c && (t += "  }  "), t += "  if (!" + _ + ") {   ";
          var E = E || [];
          E.push(t), t = "", e.createErrors !== !1 ? (t += " { keyword: 'required' , dataPath: (dataPath || '') + " + e.errorPath + " , schemaPath: " + e.util.toQuotedString(l) + " , params: { missingProperty: '" + S + "' } ", e.opts.messages !== !1 && (t += " , message: '", e.opts._errorDataPathProperty ? t += "is a required property" : t += "should have required property \\'" + S + "\\'", t += "' "), e.opts.verbose && (t += " , schema: validate.schema" + u + " , parentSchema: validate.schema" + e.schemaPath + " , data: " + m + " "), t += " } ") : t += " {} ";
          var N = t;
          t = E.pop(), !e.compositeRule && h ? e.async ? t += " throw new ValidationError([" + N + "]); " : t += " validate.errors = [" + N + "]; return false; " : t += " var err = " + N + ";  if (vErrors === null) vErrors = [err]; else vErrors.push(err); errors++; ", t += " } else { ";
        } else {
          t += " if ( ";
          var L = y;
          if (L)
            for (var Z, C = -1, re = L.length - 1; C < re; ) {
              Z = L[C += 1], C && (t += " || ");
              var fe = e.util.getProperty(Z), ne = m + fe;
              t += " ( ( " + ne + " === undefined ", O && (t += " || ! Object.prototype.hasOwnProperty.call(" + m + ", '" + e.util.escapeQuotes(Z) + "') "), t += ") && (missing" + n + " = " + e.util.toQuotedString(e.opts.jsonPointers ? Z : fe) + ") ) ";
            }
          t += ") {  ";
          var $ = "missing" + n, S = "' + " + $ + " + '";
          e.opts._errorDataPathProperty && (e.errorPath = e.opts.jsonPointers ? e.util.getPathExpr(x, $, !0) : x + " + " + $);
          var E = E || [];
          E.push(t), t = "", e.createErrors !== !1 ? (t += " { keyword: 'required' , dataPath: (dataPath || '') + " + e.errorPath + " , schemaPath: " + e.util.toQuotedString(l) + " , params: { missingProperty: '" + S + "' } ", e.opts.messages !== !1 && (t += " , message: '", e.opts._errorDataPathProperty ? t += "is a required property" : t += "should have required property \\'" + S + "\\'", t += "' "), e.opts.verbose && (t += " , schema: validate.schema" + u + " , parentSchema: validate.schema" + e.schemaPath + " , data: " + m + " "), t += " } ") : t += " {} ";
          var N = t;
          t = E.pop(), !e.compositeRule && h ? e.async ? t += " throw new ValidationError([" + N + "]); " : t += " validate.errors = [" + N + "]; return false; " : t += " var err = " + N + ";  if (vErrors === null) vErrors = [err]; else vErrors.push(err); errors++; ", t += " } else { ";
        }
      else if (T) {
        c || (t += " var " + f + " = validate.schema" + u + "; ");
        var C = "i" + n, $ = "schema" + n + "[" + C + "]", S = "' + " + $ + " + '";
        e.opts._errorDataPathProperty && (e.errorPath = e.util.getPathExpr(x, $, e.opts.jsonPointers)), c && (t += " if (" + f + " && !Array.isArray(" + f + ")) {  var err =   ", e.createErrors !== !1 ? (t += " { keyword: 'required' , dataPath: (dataPath || '') + " + e.errorPath + " , schemaPath: " + e.util.toQuotedString(l) + " , params: { missingProperty: '" + S + "' } ", e.opts.messages !== !1 && (t += " , message: '", e.opts._errorDataPathProperty ? t += "is a required property" : t += "should have required property \\'" + S + "\\'", t += "' "), e.opts.verbose && (t += " , schema: validate.schema" + u + " , parentSchema: validate.schema" + e.schemaPath + " , data: " + m + " "), t += " } ") : t += " {} ", t += ";  if (vErrors === null) vErrors = [err]; else vErrors.push(err); errors++; } else if (" + f + " !== undefined) { "), t += " for (var " + C + " = 0; " + C + " < " + f + ".length; " + C + "++) { if (" + m + "[" + f + "[" + C + "]] === undefined ", O && (t += " || ! Object.prototype.hasOwnProperty.call(" + m + ", " + f + "[" + C + "]) "), t += ") {  var err =   ", e.createErrors !== !1 ? (t += " { keyword: 'required' , dataPath: (dataPath || '') + " + e.errorPath + " , schemaPath: " + e.util.toQuotedString(l) + " , params: { missingProperty: '" + S + "' } ", e.opts.messages !== !1 && (t += " , message: '", e.opts._errorDataPathProperty ? t += "is a required property" : t += "should have required property \\'" + S + "\\'", t += "' "), e.opts.verbose && (t += " , schema: validate.schema" + u + " , parentSchema: validate.schema" + e.schemaPath + " , data: " + m + " "), t += " } ") : t += " {} ", t += ";  if (vErrors === null) vErrors = [err]; else vErrors.push(err); errors++; } } ", c && (t += "  }  ");
      } else {
        var _e = y;
        if (_e)
          for (var Z, ce = -1, de = _e.length - 1; ce < de; ) {
            Z = _e[ce += 1];
            var fe = e.util.getProperty(Z), S = e.util.escapeQuotes(Z), ne = m + fe;
            e.opts._errorDataPathProperty && (e.errorPath = e.util.getPath(x, Z, e.opts.jsonPointers)), t += " if ( " + ne + " === undefined ", O && (t += " || ! Object.prototype.hasOwnProperty.call(" + m + ", '" + e.util.escapeQuotes(Z) + "') "), t += ") {  var err =   ", e.createErrors !== !1 ? (t += " { keyword: 'required' , dataPath: (dataPath || '') + " + e.errorPath + " , schemaPath: " + e.util.toQuotedString(l) + " , params: { missingProperty: '" + S + "' } ", e.opts.messages !== !1 && (t += " , message: '", e.opts._errorDataPathProperty ? t += "is a required property" : t += "should have required property \\'" + S + "\\'", t += "' "), e.opts.verbose && (t += " , schema: validate.schema" + u + " , parentSchema: validate.schema" + e.schemaPath + " , data: " + m + " "), t += " } ") : t += " {} ", t += ";  if (vErrors === null) vErrors = [err]; else vErrors.push(err); errors++; } ";
          }
      }
      e.errorPath = x;
    } else h && (t += " if (true) {");
    return t;
  }), es;
}
var ts, Ti;
function Pd() {
  return Ti || (Ti = 1, ts = function(e, r, a) {
    var t = " ", n = e.level, i = e.dataLevel, o = e.schema[r], u = e.schemaPath + e.util.getProperty(r), l = e.errSchemaPath + "/" + r, h = !e.opts.allErrors, m = "data" + (i || ""), _ = "valid" + n, c = e.opts.$data && o && o.$data, f;
    if (c ? (t += " var schema" + n + " = " + e.util.getData(o.$data, i, e.dataPathArr) + "; ", f = "schema" + n) : f = o, (o || c) && e.opts.uniqueItems !== !1) {
      c && (t += " var " + _ + "; if (" + f + " === false || " + f + " === undefined) " + _ + " = true; else if (typeof " + f + " != 'boolean') " + _ + " = false; else { "), t += " var i = " + m + ".length , " + _ + " = true , j; if (i > 1) { ";
      var y = e.schema.items && e.schema.items.type, v = Array.isArray(y);
      if (!y || y == "object" || y == "array" || v && (y.indexOf("object") >= 0 || y.indexOf("array") >= 0))
        t += " outer: for (;i--;) { for (j = i; j--;) { if (equal(" + m + "[i], " + m + "[j])) { " + _ + " = false; break outer; } } } ";
      else {
        t += " var itemIndices = {}, item; for (;i--;) { var item = " + m + "[i]; ";
        var P = "checkDataType" + (v ? "s" : "");
        t += " if (" + e.util[P](y, "item", e.opts.strictNumbers, !0) + ") continue; ", v && (t += ` if (typeof item == 'string') item = '"' + item; `), t += " if (typeof itemIndices[item] == 'number') { " + _ + " = false; j = itemIndices[item]; break; } itemIndices[item] = i; } ";
      }
      t += " } ", c && (t += "  }  "), t += " if (!" + _ + ") {   ";
      var I = I || [];
      I.push(t), t = "", e.createErrors !== !1 ? (t += " { keyword: 'uniqueItems' , dataPath: (dataPath || '') + " + e.errorPath + " , schemaPath: " + e.util.toQuotedString(l) + " , params: { i: i, j: j } ", e.opts.messages !== !1 && (t += " , message: 'should NOT have duplicate items (items ## ' + j + ' and ' + i + ' are identical)' "), e.opts.verbose && (t += " , schema:  ", c ? t += "validate.schema" + u : t += "" + o, t += "         , parentSchema: validate.schema" + e.schemaPath + " , data: " + m + " "), t += " } ") : t += " {} ";
      var A = t;
      t = I.pop(), !e.compositeRule && h ? e.async ? t += " throw new ValidationError([" + A + "]); " : t += " validate.errors = [" + A + "]; return false; " : t += " var err = " + A + ";  if (vErrors === null) vErrors = [err]; else vErrors.push(err); errors++; ", t += " } ", h && (t += " else { ");
    } else
      h && (t += " if (true) { ");
    return t;
  }), ts;
}
var rs, Ai;
function Sd() {
  return Ai || (Ai = 1, rs = {
    $ref: ad(),
    allOf: sd(),
    anyOf: nd(),
    $comment: id(),
    const: od(),
    contains: ud(),
    dependencies: ld(),
    enum: cd(),
    format: dd(),
    if: hd(),
    items: fd(),
    maximum: pi(),
    minimum: pi(),
    maxItems: vi(),
    minItems: vi(),
    maxLength: yi(),
    minLength: yi(),
    maxProperties: bi(),
    minProperties: bi(),
    multipleOf: pd(),
    not: md(),
    oneOf: vd(),
    pattern: gd(),
    properties: yd(),
    propertyNames: _d(),
    required: bd(),
    uniqueItems: Pd(),
    validate: Oo()
  }), rs;
}
var as, Oi;
function wd() {
  if (Oi) return as;
  Oi = 1;
  var s = Sd(), e = yr().toHash;
  return as = function() {
    var a = [
      {
        type: "number",
        rules: [
          { maximum: ["exclusiveMaximum"] },
          { minimum: ["exclusiveMinimum"] },
          "multipleOf",
          "format"
        ]
      },
      {
        type: "string",
        rules: ["maxLength", "minLength", "pattern", "format"]
      },
      {
        type: "array",
        rules: ["maxItems", "minItems", "items", "contains", "uniqueItems"]
      },
      {
        type: "object",
        rules: [
          "maxProperties",
          "minProperties",
          "required",
          "dependencies",
          "propertyNames",
          { properties: ["additionalProperties", "patternProperties"] }
        ]
      },
      { rules: ["$ref", "const", "enum", "not", "anyOf", "oneOf", "allOf", "if"] }
    ], t = ["type", "$comment"], n = [
      "$schema",
      "$id",
      "id",
      "$data",
      "$async",
      "title",
      "description",
      "default",
      "definitions",
      "examples",
      "readOnly",
      "writeOnly",
      "contentMediaType",
      "contentEncoding",
      "additionalItems",
      "then",
      "else"
    ], i = ["number", "integer", "string", "array", "object", "boolean", "null"];
    return a.all = e(t), a.types = e(i), a.forEach(function(o) {
      o.rules = o.rules.map(function(u) {
        var l;
        if (typeof u == "object") {
          var h = Object.keys(u)[0];
          l = u[h], u = h, l.forEach(function(_) {
            t.push(_), a.all[_] = !0;
          });
        }
        t.push(u);
        var m = a.all[u] = {
          keyword: u,
          code: s[u],
          implements: l
        };
        return m;
      }), a.all.$comment = {
        keyword: "$comment",
        code: s.$comment
      }, o.type && (a.types[o.type] = o);
    }), a.keywords = e(t.concat(n)), a.custom = {}, a;
  }, as;
}
var ss, Ci;
function xd() {
  if (Ci) return ss;
  Ci = 1;
  var s = [
    "multipleOf",
    "maximum",
    "exclusiveMaximum",
    "minimum",
    "exclusiveMinimum",
    "maxLength",
    "minLength",
    "pattern",
    "additionalItems",
    "maxItems",
    "minItems",
    "uniqueItems",
    "maxProperties",
    "minProperties",
    "required",
    "additionalProperties",
    "enum",
    "format",
    "const"
  ];
  return ss = function(e, r) {
    for (var a = 0; a < r.length; a++) {
      e = JSON.parse(JSON.stringify(e));
      var t = r[a].split("/"), n = e, i;
      for (i = 1; i < t.length; i++)
        n = n[t[i]];
      for (i = 0; i < s.length; i++) {
        var o = s[i], u = n[o];
        u && (n[o] = {
          anyOf: [
            u,
            { $ref: "https://raw.githubusercontent.com/ajv-validator/ajv/master/lib/refs/data.json#" }
          ]
        });
      }
    }
    return e;
  }, ss;
}
var ns, $i;
function Ed() {
  if ($i) return ns;
  $i = 1;
  var s = on().MissingRef;
  ns = e;
  function e(r, a, t) {
    var n = this;
    if (typeof this._opts.loadSchema != "function")
      throw new Error("options.loadSchema should be a function");
    typeof a == "function" && (t = a, a = void 0);
    var i = o(r).then(function() {
      var l = n._addSchema(r, void 0, a);
      return l.validate || u(l);
    });
    return t && i.then(
      function(l) {
        t(null, l);
      },
      t
    ), i;
    function o(l) {
      var h = l.$schema;
      return h && !n.getSchema(h) ? e.call(n, { $ref: h }, !0) : Promise.resolve();
    }
    function u(l) {
      try {
        return n._compile(l);
      } catch (m) {
        if (m instanceof s) return h(m);
        throw m;
      }
      function h(m) {
        var _ = m.missingSchema;
        if (y(_)) throw new Error("Schema " + _ + " is loaded but " + m.missingRef + " cannot be resolved");
        var c = n._loadingSchemas[_];
        return c || (c = n._loadingSchemas[_] = n._opts.loadSchema(_), c.then(f, f)), c.then(function(v) {
          if (!y(_))
            return o(v).then(function() {
              y(_) || n.addSchema(v, _, void 0, a);
            });
        }).then(function() {
          return u(l);
        });
        function f() {
          delete n._loadingSchemas[_];
        }
        function y(v) {
          return n._refs[v] || n._schemas[v];
        }
      }
    }
  }
  return ns;
}
var is, Ii;
function Rd() {
  return Ii || (Ii = 1, is = function(e, r, a) {
    var t = " ", n = e.level, i = e.dataLevel, o = e.schema[r], u = e.schemaPath + e.util.getProperty(r), l = e.errSchemaPath + "/" + r, h = !e.opts.allErrors, m, _ = "data" + (i || ""), c = "valid" + n, f = "errs__" + n, y = e.opts.$data && o && o.$data, v;
    y ? (t += " var schema" + n + " = " + e.util.getData(o.$data, i, e.dataPathArr) + "; ", v = "schema" + n) : v = o;
    var P = this, I = "definition" + n, A = P.definition, b = "", x, T, O, C, $;
    if (y && A.$data) {
      $ = "keywordValidate" + n;
      var S = A.validateSchema;
      t += " var " + I + " = RULES.custom['" + r + "'].definition; var " + $ + " = " + I + ".validate;";
    } else {
      if (C = e.useCustomRule(P, o, e.schema, e), !C) return;
      v = "validate.schema" + u, $ = C.code, x = A.compile, T = A.inline, O = A.macro;
    }
    var E = $ + ".errors", N = "i" + n, L = "ruleErr" + n, Z = A.async;
    if (Z && !e.async) throw new Error("async keyword in sync schema");
    if (T || O || (t += "" + E + " = null;"), t += "var " + f + " = errors;var " + c + ";", y && A.$data && (b += "}", t += " if (" + v + " === undefined) { " + c + " = true; } else { ", S && (b += "}", t += " " + c + " = " + I + ".validateSchema(" + v + "); if (" + c + ") { ")), T)
      A.statements ? t += " " + C.validate + " " : t += " " + c + " = " + C.validate + "; ";
    else if (O) {
      var re = e.util.copy(e), b = "";
      re.level++;
      var fe = "valid" + re.level;
      re.schema = C.validate, re.schemaPath = "";
      var ne = e.compositeRule;
      e.compositeRule = re.compositeRule = !0;
      var _e = e.validate(re).replace(/validate\.schema/g, $);
      e.compositeRule = re.compositeRule = ne, t += " " + _e;
    } else {
      var ce = ce || [];
      ce.push(t), t = "", t += "  " + $ + ".call( ", e.opts.passContext ? t += "this" : t += "self", x || A.schema === !1 ? t += " , " + _ + " " : t += " , " + v + " , " + _ + " , validate.schema" + e.schemaPath + " ", t += " , (dataPath || '')", e.errorPath != '""' && (t += " + " + e.errorPath);
      var de = i ? "data" + (i - 1 || "") : "parentData", tt = i ? e.dataPathArr[i] : "parentDataProperty";
      t += " , " + de + " , " + tt + " , rootData )  ";
      var Ge = t;
      t = ce.pop(), A.errors === !1 ? (t += " " + c + " = ", Z && (t += "await "), t += "" + Ge + "; ") : Z ? (E = "customErrors" + n, t += " var " + E + " = null; try { " + c + " = await " + Ge + "; } catch (e) { " + c + " = false; if (e instanceof ValidationError) " + E + " = e.errors; else throw e; } ") : t += " " + E + " = null; " + c + " = " + Ge + "; ";
    }
    if (A.modifying && (t += " if (" + de + ") " + _ + " = " + de + "[" + tt + "];"), t += "" + b, A.valid)
      h && (t += " if (true) { ");
    else {
      t += " if ( ", A.valid === void 0 ? (t += " !", O ? t += "" + fe : t += "" + c) : t += " " + !A.valid + " ", t += ") { ", m = P.keyword;
      var ce = ce || [];
      ce.push(t), t = "";
      var ce = ce || [];
      ce.push(t), t = "", e.createErrors !== !1 ? (t += " { keyword: '" + (m || "custom") + "' , dataPath: (dataPath || '') + " + e.errorPath + " , schemaPath: " + e.util.toQuotedString(l) + " , params: { keyword: '" + P.keyword + "' } ", e.opts.messages !== !1 && (t += ` , message: 'should pass "` + P.keyword + `" keyword validation' `), e.opts.verbose && (t += " , schema: validate.schema" + u + " , parentSchema: validate.schema" + e.schemaPath + " , data: " + _ + " "), t += " } ") : t += " {} ";
      var Je = t;
      t = ce.pop(), !e.compositeRule && h ? e.async ? t += " throw new ValidationError([" + Je + "]); " : t += " validate.errors = [" + Je + "]; return false; " : t += " var err = " + Je + ";  if (vErrors === null) vErrors = [err]; else vErrors.push(err); errors++; ";
      var Ze = t;
      t = ce.pop(), T ? A.errors ? A.errors != "full" && (t += "  for (var " + N + "=" + f + "; " + N + "<errors; " + N + "++) { var " + L + " = vErrors[" + N + "]; if (" + L + ".dataPath === undefined) " + L + ".dataPath = (dataPath || '') + " + e.errorPath + "; if (" + L + ".schemaPath === undefined) { " + L + '.schemaPath = "' + l + '"; } ', e.opts.verbose && (t += " " + L + ".schema = " + v + "; " + L + ".data = " + _ + "; "), t += " } ") : A.errors === !1 ? t += " " + Ze + " " : (t += " if (" + f + " == errors) { " + Ze + " } else {  for (var " + N + "=" + f + "; " + N + "<errors; " + N + "++) { var " + L + " = vErrors[" + N + "]; if (" + L + ".dataPath === undefined) " + L + ".dataPath = (dataPath || '') + " + e.errorPath + "; if (" + L + ".schemaPath === undefined) { " + L + '.schemaPath = "' + l + '"; } ', e.opts.verbose && (t += " " + L + ".schema = " + v + "; " + L + ".data = " + _ + "; "), t += " } } ") : O ? (t += "   var err =   ", e.createErrors !== !1 ? (t += " { keyword: '" + (m || "custom") + "' , dataPath: (dataPath || '') + " + e.errorPath + " , schemaPath: " + e.util.toQuotedString(l) + " , params: { keyword: '" + P.keyword + "' } ", e.opts.messages !== !1 && (t += ` , message: 'should pass "` + P.keyword + `" keyword validation' `), e.opts.verbose && (t += " , schema: validate.schema" + u + " , parentSchema: validate.schema" + e.schemaPath + " , data: " + _ + " "), t += " } ") : t += " {} ", t += ";  if (vErrors === null) vErrors = [err]; else vErrors.push(err); errors++; ", !e.compositeRule && h && (e.async ? t += " throw new ValidationError(vErrors); " : t += " validate.errors = vErrors; return false; ")) : A.errors === !1 ? t += " " + Ze + " " : (t += " if (Array.isArray(" + E + ")) { if (vErrors === null) vErrors = " + E + "; else vErrors = vErrors.concat(" + E + "); errors = vErrors.length;  for (var " + N + "=" + f + "; " + N + "<errors; " + N + "++) { var " + L + " = vErrors[" + N + "]; if (" + L + ".dataPath === undefined) " + L + ".dataPath = (dataPath || '') + " + e.errorPath + ";  " + L + '.schemaPath = "' + l + '";  ', e.opts.verbose && (t += " " + L + ".schema = " + v + "; " + L + ".data = " + _ + "; "), t += " } } else { " + Ze + " } "), t += " } ", h && (t += " else { ");
    }
    return t;
  }), is;
}
const kd = "http://json-schema.org/draft-07/schema#", Td = "http://json-schema.org/draft-07/schema#", Ad = "Core schema meta-schema", Od = { schemaArray: { type: "array", minItems: 1, items: { $ref: "#" } }, nonNegativeInteger: { type: "integer", minimum: 0 }, nonNegativeIntegerDefault0: { allOf: [{ $ref: "#/definitions/nonNegativeInteger" }, { default: 0 }] }, simpleTypes: { enum: ["array", "boolean", "integer", "null", "number", "object", "string"] }, stringArray: { type: "array", items: { type: "string" }, uniqueItems: !0, default: [] } }, Cd = ["object", "boolean"], $d = { $id: { type: "string", format: "uri-reference" }, $schema: { type: "string", format: "uri" }, $ref: { type: "string", format: "uri-reference" }, $comment: { type: "string" }, title: { type: "string" }, description: { type: "string" }, default: !0, readOnly: { type: "boolean", default: !1 }, examples: { type: "array", items: !0 }, multipleOf: { type: "number", exclusiveMinimum: 0 }, maximum: { type: "number" }, exclusiveMaximum: { type: "number" }, minimum: { type: "number" }, exclusiveMinimum: { type: "number" }, maxLength: { $ref: "#/definitions/nonNegativeInteger" }, minLength: { $ref: "#/definitions/nonNegativeIntegerDefault0" }, pattern: { type: "string", format: "regex" }, additionalItems: { $ref: "#" }, items: { anyOf: [{ $ref: "#" }, { $ref: "#/definitions/schemaArray" }], default: !0 }, maxItems: { $ref: "#/definitions/nonNegativeInteger" }, minItems: { $ref: "#/definitions/nonNegativeIntegerDefault0" }, uniqueItems: { type: "boolean", default: !1 }, contains: { $ref: "#" }, maxProperties: { $ref: "#/definitions/nonNegativeInteger" }, minProperties: { $ref: "#/definitions/nonNegativeIntegerDefault0" }, required: { $ref: "#/definitions/stringArray" }, additionalProperties: { $ref: "#" }, definitions: { type: "object", additionalProperties: { $ref: "#" }, default: {} }, properties: { type: "object", additionalProperties: { $ref: "#" }, default: {} }, patternProperties: { type: "object", additionalProperties: { $ref: "#" }, propertyNames: { format: "regex" }, default: {} }, dependencies: { type: "object", additionalProperties: { anyOf: [{ $ref: "#" }, { $ref: "#/definitions/stringArray" }] } }, propertyNames: { $ref: "#" }, const: !0, enum: { type: "array", items: !0, minItems: 1, uniqueItems: !0 }, type: { anyOf: [{ $ref: "#/definitions/simpleTypes" }, { type: "array", items: { $ref: "#/definitions/simpleTypes" }, minItems: 1, uniqueItems: !0 }] }, format: { type: "string" }, contentMediaType: { type: "string" }, contentEncoding: { type: "string" }, if: { $ref: "#" }, then: { $ref: "#" }, else: { $ref: "#" }, allOf: { $ref: "#/definitions/schemaArray" }, anyOf: { $ref: "#/definitions/schemaArray" }, oneOf: { $ref: "#/definitions/schemaArray" }, not: { $ref: "#" } }, Co = {
  $schema: kd,
  $id: Td,
  title: Ad,
  definitions: Od,
  type: Cd,
  properties: $d,
  default: !0
};
var os, Ni;
function Id() {
  if (Ni) return os;
  Ni = 1;
  var s = Co;
  return os = {
    $id: "https://github.com/ajv-validator/ajv/blob/master/lib/definition_schema.js",
    definitions: {
      simpleTypes: s.definitions.simpleTypes
    },
    type: "object",
    dependencies: {
      schema: ["validate"],
      $data: ["validate"],
      statements: ["inline"],
      valid: { not: { required: ["macro"] } }
    },
    properties: {
      type: s.properties.type,
      schema: { type: "boolean" },
      statements: { type: "boolean" },
      dependencies: {
        type: "array",
        items: { type: "string" }
      },
      metaSchema: { type: "object" },
      modifying: { type: "boolean" },
      valid: { type: "boolean" },
      $data: { type: "boolean" },
      async: { type: "boolean" },
      errors: {
        anyOf: [
          { type: "boolean" },
          { const: "full" }
        ]
      }
    }
  }, os;
}
var us, Di;
function Nd() {
  if (Di) return us;
  Di = 1;
  var s = /^[a-z_$][a-z0-9_$-]*$/i, e = Rd(), r = Id();
  us = {
    add: a,
    get: t,
    remove: n,
    validate: i
  };
  function a(o, u) {
    var l = this.RULES;
    if (l.keywords[o])
      throw new Error("Keyword " + o + " is already defined");
    if (!s.test(o))
      throw new Error("Keyword " + o + " is not a valid identifier");
    if (u) {
      this.validateKeyword(u, !0);
      var h = u.type;
      if (Array.isArray(h))
        for (var m = 0; m < h.length; m++)
          c(o, h[m], u);
      else
        c(o, h, u);
      var _ = u.metaSchema;
      _ && (u.$data && this._opts.$data && (_ = {
        anyOf: [
          _,
          { $ref: "https://raw.githubusercontent.com/ajv-validator/ajv/master/lib/refs/data.json#" }
        ]
      }), u.validateSchema = this.compile(_, !0));
    }
    l.keywords[o] = l.all[o] = !0;
    function c(f, y, v) {
      for (var P, I = 0; I < l.length; I++) {
        var A = l[I];
        if (A.type == y) {
          P = A;
          break;
        }
      }
      P || (P = { type: y, rules: [] }, l.push(P));
      var b = {
        keyword: f,
        definition: v,
        custom: !0,
        code: e,
        implements: v.implements
      };
      P.rules.push(b), l.custom[f] = b;
    }
    return this;
  }
  function t(o) {
    var u = this.RULES.custom[o];
    return u ? u.definition : this.RULES.keywords[o] || !1;
  }
  function n(o) {
    var u = this.RULES;
    delete u.keywords[o], delete u.all[o], delete u.custom[o];
    for (var l = 0; l < u.length; l++)
      for (var h = u[l].rules, m = 0; m < h.length; m++)
        if (h[m].keyword == o) {
          h.splice(m, 1);
          break;
        }
    return this;
  }
  function i(o, u) {
    i.errors = null;
    var l = this._validateKeyword = this._validateKeyword || this.compile(r, !0);
    if (l(o)) return !0;
    if (i.errors = l.errors, u)
      throw new Error("custom keyword definition is invalid: " + this.errorsText(l.errors));
    return !1;
  }
  return us;
}
const Dd = "http://json-schema.org/draft-07/schema#", jd = "https://raw.githubusercontent.com/ajv-validator/ajv/master/lib/refs/data.json#", Fd = "Meta-schema for $data reference (JSON Schema extension proposal)", qd = "object", Ld = ["$data"], Zd = { $data: { type: "string", anyOf: [{ format: "relative-json-pointer" }, { format: "json-pointer" }] } }, Md = !1, zd = {
  $schema: Dd,
  $id: jd,
  description: Fd,
  type: qd,
  required: Ld,
  properties: Zd,
  additionalProperties: Md
};
var ls, ji;
function Ud() {
  if (ji) return ls;
  ji = 1;
  var s = ed(), e = nn(), r = td(), a = To(), t = Ao(), n = rd(), i = wd(), o = xd(), u = yr();
  ls = f, f.prototype.validate = y, f.prototype.compile = v, f.prototype.addSchema = P, f.prototype.addMetaSchema = I, f.prototype.validateSchema = A, f.prototype.getSchema = x, f.prototype.removeSchema = C, f.prototype.addFormat = ne, f.prototype.errorsText = fe, f.prototype._addSchema = S, f.prototype._compile = E, f.prototype.compileAsync = Ed();
  var l = Nd();
  f.prototype.addKeyword = l.add, f.prototype.getKeyword = l.get, f.prototype.removeKeyword = l.remove, f.prototype.validateKeyword = l.validate;
  var h = on();
  f.ValidationError = h.Validation, f.MissingRefError = h.MissingRef, f.$dataMetaSchema = o;
  var m = "http://json-schema.org/draft-07/schema", _ = ["removeAdditional", "useDefaults", "coerceTypes", "strictDefaults"], c = ["/properties"];
  function f(w) {
    if (!(this instanceof f)) return new f(w);
    w = this._opts = u.copy(w) || {}, Ze(this), this._schemas = {}, this._refs = {}, this._fragments = {}, this._formats = n(w.format), this._cache = w.cache || new r(), this._loadingSchemas = {}, this._compilations = [], this.RULES = i(), this._getId = N(w), w.loopRequired = w.loopRequired || 1 / 0, w.errorDataPath == "property" && (w._errorDataPathProperty = !0), w.serialize === void 0 && (w.serialize = t), this._metaOpts = Je(this), w.formats && de(this), w.keywords && tt(this), _e(this), typeof w.meta == "object" && this.addMetaSchema(w.meta), w.nullable && this.addKeyword("nullable", { metaSchema: { type: "boolean" } }), ce(this);
  }
  function y(w, D) {
    var B;
    if (typeof w == "string") {
      if (B = this.getSchema(w), !B) throw new Error('no schema with key or ref "' + w + '"');
    } else {
      var le = this._addSchema(w);
      B = le.validate || this._compile(le);
    }
    var j = B(D);
    return B.$async !== !0 && (this.errors = B.errors), j;
  }
  function v(w, D) {
    var B = this._addSchema(w, void 0, D);
    return B.validate || this._compile(B);
  }
  function P(w, D, B, le) {
    if (Array.isArray(w)) {
      for (var j = 0; j < w.length; j++) this.addSchema(w[j], void 0, B, le);
      return this;
    }
    var V = this._getId(w);
    if (V !== void 0 && typeof V != "string")
      throw new Error("schema id must be string");
    return D = e.normalizeId(D || V), Ge(this, D), this._schemas[D] = this._addSchema(w, B, le, !0), this;
  }
  function I(w, D, B) {
    return this.addSchema(w, D, B, !0), this;
  }
  function A(w, D) {
    var B = w.$schema;
    if (B !== void 0 && typeof B != "string")
      throw new Error("$schema must be a string");
    if (B = B || this._opts.defaultMeta || b(this), !B)
      return this.logger.warn("meta-schema not available"), this.errors = null, !0;
    var le = this.validate(B, w);
    if (!le && D) {
      var j = "schema is invalid: " + this.errorsText();
      if (this._opts.validateSchema == "log") this.logger.error(j);
      else throw new Error(j);
    }
    return le;
  }
  function b(w) {
    var D = w._opts.meta;
    return w._opts.defaultMeta = typeof D == "object" ? w._getId(D) || D : w.getSchema(m) ? m : void 0, w._opts.defaultMeta;
  }
  function x(w) {
    var D = O(this, w);
    switch (typeof D) {
      case "object":
        return D.validate || this._compile(D);
      case "string":
        return this.getSchema(D);
      case "undefined":
        return T(this, w);
    }
  }
  function T(w, D) {
    var B = e.schema.call(w, { schema: {} }, D);
    if (B) {
      var le = B.schema, j = B.root, V = B.baseId, ve = s.call(w, le, j, void 0, V);
      return w._fragments[D] = new a({
        ref: D,
        fragment: !0,
        schema: le,
        root: j,
        baseId: V,
        validate: ve
      }), ve;
    }
  }
  function O(w, D) {
    return D = e.normalizeId(D), w._schemas[D] || w._refs[D] || w._fragments[D];
  }
  function C(w) {
    if (w instanceof RegExp)
      return $(this, this._schemas, w), $(this, this._refs, w), this;
    switch (typeof w) {
      case "undefined":
        return $(this, this._schemas), $(this, this._refs), this._cache.clear(), this;
      case "string":
        var D = O(this, w);
        return D && this._cache.del(D.cacheKey), delete this._schemas[w], delete this._refs[w], this;
      case "object":
        var B = this._opts.serialize, le = B ? B(w) : w;
        this._cache.del(le);
        var j = this._getId(w);
        j && (j = e.normalizeId(j), delete this._schemas[j], delete this._refs[j]);
    }
    return this;
  }
  function $(w, D, B) {
    for (var le in D) {
      var j = D[le];
      !j.meta && (!B || B.test(le)) && (w._cache.del(j.cacheKey), delete D[le]);
    }
  }
  function S(w, D, B, le) {
    if (typeof w != "object" && typeof w != "boolean")
      throw new Error("schema should be object or boolean");
    var j = this._opts.serialize, V = j ? j(w) : w, ve = this._cache.get(V);
    if (ve) return ve;
    le = le || this._opts.addUsedSchema !== !1;
    var Se = e.normalizeId(this._getId(w));
    Se && le && Ge(this, Se);
    var be = this._opts.validateSchema !== !1 && !D, Ne;
    be && !(Ne = Se && Se == e.normalizeId(w.$schema)) && this.validateSchema(w, !0);
    var Te = e.ids.call(this, w), Ae = new a({
      id: Se,
      schema: w,
      localRefs: Te,
      cacheKey: V,
      meta: B
    });
    return Se[0] != "#" && le && (this._refs[Se] = Ae), this._cache.put(V, Ae), be && Ne && this.validateSchema(w, !0), Ae;
  }
  function E(w, D) {
    if (w.compiling)
      return w.validate = j, j.schema = w.schema, j.errors = null, j.root = D || j, w.schema.$async === !0 && (j.$async = !0), j;
    w.compiling = !0;
    var B;
    w.meta && (B = this._opts, this._opts = this._metaOpts);
    var le;
    try {
      le = s.call(this, w.schema, D, w.localRefs);
    } catch (V) {
      throw delete w.validate, V;
    } finally {
      w.compiling = !1, w.meta && (this._opts = B);
    }
    return w.validate = le, w.refs = le.refs, w.refVal = le.refVal, w.root = le.root, le;
    function j() {
      var V = w.validate, ve = V.apply(this, arguments);
      return j.errors = V.errors, ve;
    }
  }
  function N(w) {
    switch (w.schemaId) {
      case "auto":
        return re;
      case "id":
        return L;
      default:
        return Z;
    }
  }
  function L(w) {
    return w.$id && this.logger.warn("schema $id ignored", w.$id), w.id;
  }
  function Z(w) {
    return w.id && this.logger.warn("schema id ignored", w.id), w.$id;
  }
  function re(w) {
    if (w.$id && w.id && w.$id != w.id)
      throw new Error("schema $id is different from id");
    return w.$id || w.id;
  }
  function fe(w, D) {
    if (w = w || this.errors, !w) return "No errors";
    D = D || {};
    for (var B = D.separator === void 0 ? ", " : D.separator, le = D.dataVar === void 0 ? "data" : D.dataVar, j = "", V = 0; V < w.length; V++) {
      var ve = w[V];
      ve && (j += le + ve.dataPath + " " + ve.message + B);
    }
    return j.slice(0, -B.length);
  }
  function ne(w, D) {
    return typeof D == "string" && (D = new RegExp(D)), this._formats[w] = D, this;
  }
  function _e(w) {
    var D;
    if (w._opts.$data && (D = zd, w.addMetaSchema(D, D.$id, !0)), w._opts.meta !== !1) {
      var B = Co;
      w._opts.$data && (B = o(B, c)), w.addMetaSchema(B, m, !0), w._refs["http://json-schema.org/schema"] = m;
    }
  }
  function ce(w) {
    var D = w._opts.schemas;
    if (D)
      if (Array.isArray(D)) w.addSchema(D);
      else for (var B in D) w.addSchema(D[B], B);
  }
  function de(w) {
    for (var D in w._opts.formats) {
      var B = w._opts.formats[D];
      w.addFormat(D, B);
    }
  }
  function tt(w) {
    for (var D in w._opts.keywords) {
      var B = w._opts.keywords[D];
      w.addKeyword(D, B);
    }
  }
  function Ge(w, D) {
    if (w._schemas[D] || w._refs[D])
      throw new Error('schema with key or id "' + D + '" already exists');
  }
  function Je(w) {
    for (var D = u.copy(w._opts), B = 0; B < _.length; B++)
      delete D[_[B]];
    return D;
  }
  function Ze(w) {
    var D = w._opts.logger;
    if (D === !1)
      w.logger = { log: mt, warn: mt, error: mt };
    else {
      if (D === void 0 && (D = console), !(typeof D == "object" && D.log && D.warn && D.error))
        throw new Error("logger must implement log, warn and error methods");
      w.logger = D;
    }
  }
  function mt() {
  }
  return ls;
}
var Vd = Ud();
const Hd = /* @__PURE__ */ Jc(Vd);
class Bd extends Qc {
  /**
   * Initializes this server with the given name and version information.
   */
  constructor(e, r) {
    var a;
    super(r), this._serverInfo = e, this._capabilities = (a = r == null ? void 0 : r.capabilities) !== null && a !== void 0 ? a : {}, this._instructions = r == null ? void 0 : r.instructions, this.setRequestHandler(vo, (t) => this._oninitialize(t)), this.setNotificationHandler(go, () => {
      var t;
      return (t = this.oninitialized) === null || t === void 0 ? void 0 : t.call(this);
    });
  }
  /**
   * Registers new capabilities. This can only be called before connecting to a transport.
   *
   * The new capabilities will be merged with any existing capabilities previously given (e.g., at initialization).
   */
  registerCapabilities(e) {
    if (this.transport)
      throw new Error("Cannot register capabilities after connecting to transport");
    this._capabilities = Kc(this._capabilities, e);
  }
  assertCapabilityForMethod(e) {
    var r, a, t;
    switch (e) {
      case "sampling/createMessage":
        if (!(!((r = this._clientCapabilities) === null || r === void 0) && r.sampling))
          throw new Error(`Client does not support sampling (required for ${e})`);
        break;
      case "elicitation/create":
        if (!(!((a = this._clientCapabilities) === null || a === void 0) && a.elicitation))
          throw new Error(`Client does not support elicitation (required for ${e})`);
        break;
      case "roots/list":
        if (!(!((t = this._clientCapabilities) === null || t === void 0) && t.roots))
          throw new Error(`Client does not support listing roots (required for ${e})`);
        break;
    }
  }
  assertNotificationCapability(e) {
    switch (e) {
      case "notifications/message":
        if (!this._capabilities.logging)
          throw new Error(`Server does not support logging (required for ${e})`);
        break;
      case "notifications/resources/updated":
      case "notifications/resources/list_changed":
        if (!this._capabilities.resources)
          throw new Error(`Server does not support notifying about resources (required for ${e})`);
        break;
      case "notifications/tools/list_changed":
        if (!this._capabilities.tools)
          throw new Error(`Server does not support notifying of tool list changes (required for ${e})`);
        break;
      case "notifications/prompts/list_changed":
        if (!this._capabilities.prompts)
          throw new Error(`Server does not support notifying of prompt list changes (required for ${e})`);
        break;
    }
  }
  assertRequestHandlerCapability(e) {
    switch (e) {
      case "sampling/createMessage":
        if (!this._capabilities.sampling)
          throw new Error(`Server does not support sampling (required for ${e})`);
        break;
      case "logging/setLevel":
        if (!this._capabilities.logging)
          throw new Error(`Server does not support logging (required for ${e})`);
        break;
      case "prompts/get":
      case "prompts/list":
        if (!this._capabilities.prompts)
          throw new Error(`Server does not support prompts (required for ${e})`);
        break;
      case "resources/list":
      case "resources/templates/list":
      case "resources/read":
        if (!this._capabilities.resources)
          throw new Error(`Server does not support resources (required for ${e})`);
        break;
      case "tools/call":
      case "tools/list":
        if (!this._capabilities.tools)
          throw new Error(`Server does not support tools (required for ${e})`);
        break;
    }
  }
  async _oninitialize(e) {
    const r = e.params.protocolVersion;
    return this._clientCapabilities = e.params.capabilities, this._clientVersion = e.params.clientInfo, {
      protocolVersion: Xl.includes(r) ? r : oo,
      capabilities: this.getCapabilities(),
      serverInfo: this._serverInfo,
      ...this._instructions && { instructions: this._instructions }
    };
  }
  /**
   * After initialization has completed, this will be populated with the client's reported capabilities.
   */
  getClientCapabilities() {
    return this._clientCapabilities;
  }
  /**
   * After initialization has completed, this will be populated with information about the client's name and version.
   */
  getClientVersion() {
    return this._clientVersion;
  }
  getCapabilities() {
    return this._capabilities;
  }
  async ping() {
    return this.request({ method: "ping" }, Ws);
  }
  async createMessage(e, r) {
    return this.request({ method: "sampling/createMessage", params: e }, Eo, r);
  }
  async elicitInput(e, r) {
    const a = await this.request({ method: "elicitation/create", params: e }, Ro, r);
    if (a.action === "accept" && a.content)
      try {
        const t = new Hd(), n = t.compile(e.requestedSchema);
        if (!n(a.content))
          throw new Ve(qe.InvalidParams, `Elicitation response content does not match requested schema: ${t.errorsText(n.errors)}`);
      } catch (t) {
        throw t instanceof Ve ? t : new Ve(qe.InternalError, `Error validating elicitation response: ${t}`);
      }
    return a;
  }
  async listRoots(e, r) {
    return this.request({ method: "roots/list", params: e }, ko, r);
  }
  async sendLoggingMessage(e) {
    return this.notification({ method: "notifications/message", params: e });
  }
  async sendResourceUpdated(e) {
    return this.notification({
      method: "notifications/resources/updated",
      params: e
    });
  }
  async sendResourceListChanged() {
    return this.notification({
      method: "notifications/resources/list_changed"
    });
  }
  async sendToolListChanged() {
    return this.notification({ method: "notifications/tools/list_changed" });
  }
  async sendPromptListChanged() {
    return this.notification({ method: "notifications/prompts/list_changed" });
  }
}
const Qd = Symbol("Let zodToJsonSchema decide on which parser to use"), Fi = {
  name: void 0,
  $refStrategy: "root",
  basePath: ["#"],
  effectStrategy: "input",
  pipeStrategy: "all",
  dateStrategy: "format:date-time",
  mapStrategy: "entries",
  removeAdditionalStrategy: "passthrough",
  allowedAdditionalProperties: !0,
  rejectedAdditionalProperties: !1,
  definitionPath: "definitions",
  target: "jsonSchema7",
  strictUnions: !1,
  definitions: {},
  errorMessages: !1,
  markdownDescription: !1,
  patternStrategy: "escape",
  applyRegexFlags: !1,
  emailStrategy: "format:email",
  base64Strategy: "contentEncoding:base64",
  nameStrategy: "ref",
  openAiAnyTypeName: "OpenAiAnyType"
}, Kd = (s) => typeof s == "string" ? {
  ...Fi,
  name: s
} : {
  ...Fi,
  ...s
}, Jd = (s) => {
  const e = Kd(s), r = e.name !== void 0 ? [...e.basePath, e.definitionPath, e.name] : e.basePath;
  return {
    ...e,
    flags: { hasReferencedOpenAiAnyType: !1 },
    currentPath: r,
    propertyPath: void 0,
    seen: new Map(Object.entries(e.definitions).map(([a, t]) => [
      t._def,
      {
        def: t._def,
        path: [...e.basePath, e.definitionPath, a],
        // Resolution of references will be forced even though seen, so it's ok that the schema is undefined here for now.
        jsonSchema: void 0
      }
    ]))
  };
};
function $o(s, e, r, a) {
  a != null && a.errorMessages && r && (s.errorMessage = {
    ...s.errorMessage,
    [e]: r
  });
}
function Ce(s, e, r, a, t) {
  s[e] = r, $o(s, e, a, t);
}
const Io = (s, e) => {
  let r = 0;
  for (; r < s.length && r < e.length && s[r] === e[r]; r++)
    ;
  return [(s.length - r).toString(), ...e.slice(r)].join("/");
};
function dt(s) {
  if (s.target !== "openAi")
    return {};
  const e = [
    ...s.basePath,
    s.definitionPath,
    s.openAiAnyTypeName
  ];
  return s.flags.hasReferencedOpenAiAnyType = !0, {
    $ref: s.$refStrategy === "relative" ? Io(e, s.currentPath) : e.join("/")
  };
}
function Wd(s, e) {
  var a, t, n;
  const r = {
    type: "array"
  };
  return (a = s.type) != null && a._def && ((n = (t = s.type) == null ? void 0 : t._def) == null ? void 0 : n.typeName) !== U.ZodAny && (r.items = Oe(s.type._def, {
    ...e,
    currentPath: [...e.currentPath, "items"]
  })), s.minLength && Ce(r, "minItems", s.minLength.value, s.minLength.message, e), s.maxLength && Ce(r, "maxItems", s.maxLength.value, s.maxLength.message, e), s.exactLength && (Ce(r, "minItems", s.exactLength.value, s.exactLength.message, e), Ce(r, "maxItems", s.exactLength.value, s.exactLength.message, e)), r;
}
function Gd(s, e) {
  const r = {
    type: "integer",
    format: "int64"
  };
  if (!s.checks)
    return r;
  for (const a of s.checks)
    switch (a.kind) {
      case "min":
        e.target === "jsonSchema7" ? a.inclusive ? Ce(r, "minimum", a.value, a.message, e) : Ce(r, "exclusiveMinimum", a.value, a.message, e) : (a.inclusive || (r.exclusiveMinimum = !0), Ce(r, "minimum", a.value, a.message, e));
        break;
      case "max":
        e.target === "jsonSchema7" ? a.inclusive ? Ce(r, "maximum", a.value, a.message, e) : Ce(r, "exclusiveMaximum", a.value, a.message, e) : (a.inclusive || (r.exclusiveMaximum = !0), Ce(r, "maximum", a.value, a.message, e));
        break;
      case "multipleOf":
        Ce(r, "multipleOf", a.value, a.message, e);
        break;
    }
  return r;
}
function Yd() {
  return {
    type: "boolean"
  };
}
function No(s, e) {
  return Oe(s.type._def, e);
}
const Xd = (s, e) => Oe(s.innerType._def, e);
function Do(s, e, r) {
  const a = r ?? e.dateStrategy;
  if (Array.isArray(a))
    return {
      anyOf: a.map((t, n) => Do(s, e, t))
    };
  switch (a) {
    case "string":
    case "format:date-time":
      return {
        type: "string",
        format: "date-time"
      };
    case "format:date":
      return {
        type: "string",
        format: "date"
      };
    case "integer":
      return eh(s, e);
  }
}
const eh = (s, e) => {
  const r = {
    type: "integer",
    format: "unix-time"
  };
  if (e.target === "openApi3")
    return r;
  for (const a of s.checks)
    switch (a.kind) {
      case "min":
        Ce(
          r,
          "minimum",
          a.value,
          // This is in milliseconds
          a.message,
          e
        );
        break;
      case "max":
        Ce(
          r,
          "maximum",
          a.value,
          // This is in milliseconds
          a.message,
          e
        );
        break;
    }
  return r;
};
function th(s, e) {
  return {
    ...Oe(s.innerType._def, e),
    default: s.defaultValue()
  };
}
function rh(s, e) {
  return e.effectStrategy === "input" ? Oe(s.schema._def, e) : dt(e);
}
function ah(s) {
  return {
    type: "string",
    enum: Array.from(s.values)
  };
}
const sh = (s) => "type" in s && s.type === "string" ? !1 : "allOf" in s;
function nh(s, e) {
  const r = [
    Oe(s.left._def, {
      ...e,
      currentPath: [...e.currentPath, "allOf", "0"]
    }),
    Oe(s.right._def, {
      ...e,
      currentPath: [...e.currentPath, "allOf", "1"]
    })
  ].filter((n) => !!n);
  let a = e.target === "jsonSchema2019-09" ? { unevaluatedProperties: !1 } : void 0;
  const t = [];
  return r.forEach((n) => {
    if (sh(n))
      t.push(...n.allOf), n.unevaluatedProperties === void 0 && (a = void 0);
    else {
      let i = n;
      if ("additionalProperties" in n && n.additionalProperties === !1) {
        const { additionalProperties: o, ...u } = n;
        i = u;
      } else
        a = void 0;
      t.push(i);
    }
  }), t.length ? {
    allOf: t,
    ...a
  } : void 0;
}
function ih(s, e) {
  const r = typeof s.value;
  return r !== "bigint" && r !== "number" && r !== "boolean" && r !== "string" ? {
    type: Array.isArray(s.value) ? "array" : "object"
  } : e.target === "openApi3" ? {
    type: r === "bigint" ? "integer" : r,
    enum: [s.value]
  } : {
    type: r === "bigint" ? "integer" : r,
    const: s.value
  };
}
let cs;
const xt = {
  /**
   * `c` was changed to `[cC]` to replicate /i flag
   */
  cuid: /^[cC][^\s-]{8,}$/,
  cuid2: /^[0-9a-z]+$/,
  ulid: /^[0-9A-HJKMNP-TV-Z]{26}$/,
  /**
   * `a-z` was added to replicate /i flag
   */
  email: /^(?!\.)(?!.*\.\.)([a-zA-Z0-9_'+\-\.]*)[a-zA-Z0-9_+-]@([a-zA-Z0-9][a-zA-Z0-9\-]*\.)+[a-zA-Z]{2,}$/,
  /**
   * Constructed a valid Unicode RegExp
   *
   * Lazily instantiate since this type of regex isn't supported
   * in all envs (e.g. React Native).
   *
   * See:
   * https://github.com/colinhacks/zod/issues/2433
   * Fix in Zod:
   * https://github.com/colinhacks/zod/commit/9340fd51e48576a75adc919bff65dbc4a5d4c99b
   */
  emoji: () => (cs === void 0 && (cs = RegExp("^(\\p{Extended_Pictographic}|\\p{Emoji_Component})+$", "u")), cs),
  /**
   * Unused
   */
  uuid: /^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$/,
  /**
   * Unused
   */
  ipv4: /^(?:(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])\.){3}(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])$/,
  ipv4Cidr: /^(?:(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])\.){3}(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])\/(3[0-2]|[12]?[0-9])$/,
  /**
   * Unused
   */
  ipv6: /^(([a-f0-9]{1,4}:){7}|::([a-f0-9]{1,4}:){0,6}|([a-f0-9]{1,4}:){1}:([a-f0-9]{1,4}:){0,5}|([a-f0-9]{1,4}:){2}:([a-f0-9]{1,4}:){0,4}|([a-f0-9]{1,4}:){3}:([a-f0-9]{1,4}:){0,3}|([a-f0-9]{1,4}:){4}:([a-f0-9]{1,4}:){0,2}|([a-f0-9]{1,4}:){5}:([a-f0-9]{1,4}:){0,1})([a-f0-9]{1,4}|(((25[0-5])|(2[0-4][0-9])|(1[0-9]{2})|([0-9]{1,2}))\.){3}((25[0-5])|(2[0-4][0-9])|(1[0-9]{2})|([0-9]{1,2})))$/,
  ipv6Cidr: /^(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))\/(12[0-8]|1[01][0-9]|[1-9]?[0-9])$/,
  base64: /^([0-9a-zA-Z+/]{4})*(([0-9a-zA-Z+/]{2}==)|([0-9a-zA-Z+/]{3}=))?$/,
  base64url: /^([0-9a-zA-Z-_]{4})*(([0-9a-zA-Z-_]{2}(==)?)|([0-9a-zA-Z-_]{3}(=)?))?$/,
  nanoid: /^[a-zA-Z0-9_-]{21}$/,
  jwt: /^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]*$/
};
function jo(s, e) {
  const r = {
    type: "string"
  };
  if (s.checks)
    for (const a of s.checks)
      switch (a.kind) {
        case "min":
          Ce(r, "minLength", typeof r.minLength == "number" ? Math.max(r.minLength, a.value) : a.value, a.message, e);
          break;
        case "max":
          Ce(r, "maxLength", typeof r.maxLength == "number" ? Math.min(r.maxLength, a.value) : a.value, a.message, e);
          break;
        case "email":
          switch (e.emailStrategy) {
            case "format:email":
              Et(r, "email", a.message, e);
              break;
            case "format:idn-email":
              Et(r, "idn-email", a.message, e);
              break;
            case "pattern:zod":
              at(r, xt.email, a.message, e);
              break;
          }
          break;
        case "url":
          Et(r, "uri", a.message, e);
          break;
        case "uuid":
          Et(r, "uuid", a.message, e);
          break;
        case "regex":
          at(r, a.regex, a.message, e);
          break;
        case "cuid":
          at(r, xt.cuid, a.message, e);
          break;
        case "cuid2":
          at(r, xt.cuid2, a.message, e);
          break;
        case "startsWith":
          at(r, RegExp(`^${ds(a.value, e)}`), a.message, e);
          break;
        case "endsWith":
          at(r, RegExp(`${ds(a.value, e)}$`), a.message, e);
          break;
        case "datetime":
          Et(r, "date-time", a.message, e);
          break;
        case "date":
          Et(r, "date", a.message, e);
          break;
        case "time":
          Et(r, "time", a.message, e);
          break;
        case "duration":
          Et(r, "duration", a.message, e);
          break;
        case "length":
          Ce(r, "minLength", typeof r.minLength == "number" ? Math.max(r.minLength, a.value) : a.value, a.message, e), Ce(r, "maxLength", typeof r.maxLength == "number" ? Math.min(r.maxLength, a.value) : a.value, a.message, e);
          break;
        case "includes": {
          at(r, RegExp(ds(a.value, e)), a.message, e);
          break;
        }
        case "ip": {
          a.version !== "v6" && Et(r, "ipv4", a.message, e), a.version !== "v4" && Et(r, "ipv6", a.message, e);
          break;
        }
        case "base64url":
          at(r, xt.base64url, a.message, e);
          break;
        case "jwt":
          at(r, xt.jwt, a.message, e);
          break;
        case "cidr": {
          a.version !== "v6" && at(r, xt.ipv4Cidr, a.message, e), a.version !== "v4" && at(r, xt.ipv6Cidr, a.message, e);
          break;
        }
        case "emoji":
          at(r, xt.emoji(), a.message, e);
          break;
        case "ulid": {
          at(r, xt.ulid, a.message, e);
          break;
        }
        case "base64": {
          switch (e.base64Strategy) {
            case "format:binary": {
              Et(r, "binary", a.message, e);
              break;
            }
            case "contentEncoding:base64": {
              Ce(r, "contentEncoding", "base64", a.message, e);
              break;
            }
            case "pattern:zod": {
              at(r, xt.base64, a.message, e);
              break;
            }
          }
          break;
        }
        case "nanoid":
          at(r, xt.nanoid, a.message, e);
      }
  return r;
}
function ds(s, e) {
  return e.patternStrategy === "escape" ? uh(s) : s;
}
const oh = new Set("ABCDEFGHIJKLMNOPQRSTUVXYZabcdefghijklmnopqrstuvxyz0123456789");
function uh(s) {
  let e = "";
  for (let r = 0; r < s.length; r++)
    oh.has(s[r]) || (e += "\\"), e += s[r];
  return e;
}
function Et(s, e, r, a) {
  var t;
  s.format || (t = s.anyOf) != null && t.some((n) => n.format) ? (s.anyOf || (s.anyOf = []), s.format && (s.anyOf.push({
    format: s.format,
    ...s.errorMessage && a.errorMessages && {
      errorMessage: { format: s.errorMessage.format }
    }
  }), delete s.format, s.errorMessage && (delete s.errorMessage.format, Object.keys(s.errorMessage).length === 0 && delete s.errorMessage)), s.anyOf.push({
    format: e,
    ...r && a.errorMessages && { errorMessage: { format: r } }
  })) : Ce(s, "format", e, r, a);
}
function at(s, e, r, a) {
  var t;
  s.pattern || (t = s.allOf) != null && t.some((n) => n.pattern) ? (s.allOf || (s.allOf = []), s.pattern && (s.allOf.push({
    pattern: s.pattern,
    ...s.errorMessage && a.errorMessages && {
      errorMessage: { pattern: s.errorMessage.pattern }
    }
  }), delete s.pattern, s.errorMessage && (delete s.errorMessage.pattern, Object.keys(s.errorMessage).length === 0 && delete s.errorMessage)), s.allOf.push({
    pattern: qi(e, a),
    ...r && a.errorMessages && { errorMessage: { pattern: r } }
  })) : Ce(s, "pattern", qi(e, a), r, a);
}
function qi(s, e) {
  var u;
  if (!e.applyRegexFlags || !s.flags)
    return s.source;
  const r = {
    i: s.flags.includes("i"),
    m: s.flags.includes("m"),
    s: s.flags.includes("s")
    // `.` matches newlines
  }, a = r.i ? s.source.toLowerCase() : s.source;
  let t = "", n = !1, i = !1, o = !1;
  for (let l = 0; l < a.length; l++) {
    if (n) {
      t += a[l], n = !1;
      continue;
    }
    if (r.i) {
      if (i) {
        if (a[l].match(/[a-z]/)) {
          o ? (t += a[l], t += `${a[l - 2]}-${a[l]}`.toUpperCase(), o = !1) : a[l + 1] === "-" && ((u = a[l + 2]) != null && u.match(/[a-z]/)) ? (t += a[l], o = !0) : t += `${a[l]}${a[l].toUpperCase()}`;
          continue;
        }
      } else if (a[l].match(/[a-z]/)) {
        t += `[${a[l]}${a[l].toUpperCase()}]`;
        continue;
      }
    }
    if (r.m) {
      if (a[l] === "^") {
        t += `(^|(?<=[\r
]))`;
        continue;
      } else if (a[l] === "$") {
        t += `($|(?=[\r
]))`;
        continue;
      }
    }
    if (r.s && a[l] === ".") {
      t += i ? `${a[l]}\r
` : `[${a[l]}\r
]`;
      continue;
    }
    t += a[l], a[l] === "\\" ? n = !0 : i && a[l] === "]" ? i = !1 : !i && a[l] === "[" && (i = !0);
  }
  try {
    new RegExp(t);
  } catch {
    return console.warn(`Could not convert regex pattern at ${e.currentPath.join("/")} to a flag-independent form! Falling back to the flag-ignorant source`), s.source;
  }
  return t;
}
function Fo(s, e) {
  var a, t, n, i, o, u;
  if (e.target === "openAi" && console.warn("Warning: OpenAI may not support records in schemas! Try an array of key-value pairs instead."), e.target === "openApi3" && ((a = s.keyType) == null ? void 0 : a._def.typeName) === U.ZodEnum)
    return {
      type: "object",
      required: s.keyType._def.values,
      properties: s.keyType._def.values.reduce((l, h) => ({
        ...l,
        [h]: Oe(s.valueType._def, {
          ...e,
          currentPath: [...e.currentPath, "properties", h]
        }) ?? dt(e)
      }), {}),
      additionalProperties: e.rejectedAdditionalProperties
    };
  const r = {
    type: "object",
    additionalProperties: Oe(s.valueType._def, {
      ...e,
      currentPath: [...e.currentPath, "additionalProperties"]
    }) ?? e.allowedAdditionalProperties
  };
  if (e.target === "openApi3")
    return r;
  if (((t = s.keyType) == null ? void 0 : t._def.typeName) === U.ZodString && ((n = s.keyType._def.checks) != null && n.length)) {
    const { type: l, ...h } = jo(s.keyType._def, e);
    return {
      ...r,
      propertyNames: h
    };
  } else {
    if (((i = s.keyType) == null ? void 0 : i._def.typeName) === U.ZodEnum)
      return {
        ...r,
        propertyNames: {
          enum: s.keyType._def.values
        }
      };
    if (((o = s.keyType) == null ? void 0 : o._def.typeName) === U.ZodBranded && s.keyType._def.type._def.typeName === U.ZodString && ((u = s.keyType._def.type._def.checks) != null && u.length)) {
      const { type: l, ...h } = No(s.keyType._def, e);
      return {
        ...r,
        propertyNames: h
      };
    }
  }
  return r;
}
function lh(s, e) {
  if (e.mapStrategy === "record")
    return Fo(s, e);
  const r = Oe(s.keyType._def, {
    ...e,
    currentPath: [...e.currentPath, "items", "items", "0"]
  }) || dt(e), a = Oe(s.valueType._def, {
    ...e,
    currentPath: [...e.currentPath, "items", "items", "1"]
  }) || dt(e);
  return {
    type: "array",
    maxItems: 125,
    items: {
      type: "array",
      items: [r, a],
      minItems: 2,
      maxItems: 2
    }
  };
}
function ch(s) {
  const e = s.values, a = Object.keys(s.values).filter((n) => typeof e[e[n]] != "number").map((n) => e[n]), t = Array.from(new Set(a.map((n) => typeof n)));
  return {
    type: t.length === 1 ? t[0] === "string" ? "string" : "number" : ["string", "number"],
    enum: a
  };
}
function dh(s) {
  return s.target === "openAi" ? void 0 : {
    not: dt({
      ...s,
      currentPath: [...s.currentPath, "not"]
    })
  };
}
function hh(s) {
  return s.target === "openApi3" ? {
    enum: ["null"],
    nullable: !0
  } : {
    type: "null"
  };
}
const oa = {
  ZodString: "string",
  ZodNumber: "number",
  ZodBigInt: "integer",
  ZodBoolean: "boolean",
  ZodNull: "null"
};
function fh(s, e) {
  if (e.target === "openApi3")
    return Li(s, e);
  const r = s.options instanceof Map ? Array.from(s.options.values()) : s.options;
  if (r.every((a) => a._def.typeName in oa && (!a._def.checks || !a._def.checks.length))) {
    const a = r.reduce((t, n) => {
      const i = oa[n._def.typeName];
      return i && !t.includes(i) ? [...t, i] : t;
    }, []);
    return {
      type: a.length > 1 ? a : a[0]
    };
  } else if (r.every((a) => a._def.typeName === "ZodLiteral" && !a.description)) {
    const a = r.reduce((t, n) => {
      const i = typeof n._def.value;
      switch (i) {
        case "string":
        case "number":
        case "boolean":
          return [...t, i];
        case "bigint":
          return [...t, "integer"];
        case "object":
          if (n._def.value === null)
            return [...t, "null"];
        case "symbol":
        case "undefined":
        case "function":
        default:
          return t;
      }
    }, []);
    if (a.length === r.length) {
      const t = a.filter((n, i, o) => o.indexOf(n) === i);
      return {
        type: t.length > 1 ? t : t[0],
        enum: r.reduce((n, i) => n.includes(i._def.value) ? n : [...n, i._def.value], [])
      };
    }
  } else if (r.every((a) => a._def.typeName === "ZodEnum"))
    return {
      type: "string",
      enum: r.reduce((a, t) => [
        ...a,
        ...t._def.values.filter((n) => !a.includes(n))
      ], [])
    };
  return Li(s, e);
}
const Li = (s, e) => {
  const r = (s.options instanceof Map ? Array.from(s.options.values()) : s.options).map((a, t) => Oe(a._def, {
    ...e,
    currentPath: [...e.currentPath, "anyOf", `${t}`]
  })).filter((a) => !!a && (!e.strictUnions || typeof a == "object" && Object.keys(a).length > 0));
  return r.length ? { anyOf: r } : void 0;
};
function ph(s, e) {
  if (["ZodString", "ZodNumber", "ZodBigInt", "ZodBoolean", "ZodNull"].includes(s.innerType._def.typeName) && (!s.innerType._def.checks || !s.innerType._def.checks.length))
    return e.target === "openApi3" ? {
      type: oa[s.innerType._def.typeName],
      nullable: !0
    } : {
      type: [
        oa[s.innerType._def.typeName],
        "null"
      ]
    };
  if (e.target === "openApi3") {
    const a = Oe(s.innerType._def, {
      ...e,
      currentPath: [...e.currentPath]
    });
    return a && "$ref" in a ? { allOf: [a], nullable: !0 } : a && { ...a, nullable: !0 };
  }
  const r = Oe(s.innerType._def, {
    ...e,
    currentPath: [...e.currentPath, "anyOf", "0"]
  });
  return r && { anyOf: [r, { type: "null" }] };
}
function mh(s, e) {
  const r = {
    type: "number"
  };
  if (!s.checks)
    return r;
  for (const a of s.checks)
    switch (a.kind) {
      case "int":
        r.type = "integer", $o(r, "type", a.message, e);
        break;
      case "min":
        e.target === "jsonSchema7" ? a.inclusive ? Ce(r, "minimum", a.value, a.message, e) : Ce(r, "exclusiveMinimum", a.value, a.message, e) : (a.inclusive || (r.exclusiveMinimum = !0), Ce(r, "minimum", a.value, a.message, e));
        break;
      case "max":
        e.target === "jsonSchema7" ? a.inclusive ? Ce(r, "maximum", a.value, a.message, e) : Ce(r, "exclusiveMaximum", a.value, a.message, e) : (a.inclusive || (r.exclusiveMaximum = !0), Ce(r, "maximum", a.value, a.message, e));
        break;
      case "multipleOf":
        Ce(r, "multipleOf", a.value, a.message, e);
        break;
    }
  return r;
}
function vh(s, e) {
  const r = e.target === "openAi", a = {
    type: "object",
    properties: {}
  }, t = [], n = s.shape();
  for (const o in n) {
    let u = n[o];
    if (u === void 0 || u._def === void 0)
      continue;
    let l = yh(u);
    l && r && (u._def.typeName === "ZodOptional" && (u = u._def.innerType), u.isNullable() || (u = u.nullable()), l = !1);
    const h = Oe(u._def, {
      ...e,
      currentPath: [...e.currentPath, "properties", o],
      propertyPath: [...e.currentPath, "properties", o]
    });
    h !== void 0 && (a.properties[o] = h, l || t.push(o));
  }
  t.length && (a.required = t);
  const i = gh(s, e);
  return i !== void 0 && (a.additionalProperties = i), a;
}
function gh(s, e) {
  if (s.catchall._def.typeName !== "ZodNever")
    return Oe(s.catchall._def, {
      ...e,
      currentPath: [...e.currentPath, "additionalProperties"]
    });
  switch (s.unknownKeys) {
    case "passthrough":
      return e.allowedAdditionalProperties;
    case "strict":
      return e.rejectedAdditionalProperties;
    case "strip":
      return e.removeAdditionalStrategy === "strict" ? e.allowedAdditionalProperties : e.rejectedAdditionalProperties;
  }
}
function yh(s) {
  try {
    return s.isOptional();
  } catch {
    return !0;
  }
}
const _h = (s, e) => {
  var a;
  if (e.currentPath.toString() === ((a = e.propertyPath) == null ? void 0 : a.toString()))
    return Oe(s.innerType._def, e);
  const r = Oe(s.innerType._def, {
    ...e,
    currentPath: [...e.currentPath, "anyOf", "1"]
  });
  return r ? {
    anyOf: [
      {
        not: dt(e)
      },
      r
    ]
  } : dt(e);
}, bh = (s, e) => {
  if (e.pipeStrategy === "input")
    return Oe(s.in._def, e);
  if (e.pipeStrategy === "output")
    return Oe(s.out._def, e);
  const r = Oe(s.in._def, {
    ...e,
    currentPath: [...e.currentPath, "allOf", "0"]
  }), a = Oe(s.out._def, {
    ...e,
    currentPath: [...e.currentPath, "allOf", r ? "1" : "0"]
  });
  return {
    allOf: [r, a].filter((t) => t !== void 0)
  };
};
function Ph(s, e) {
  return Oe(s.type._def, e);
}
function Sh(s, e) {
  const a = {
    type: "array",
    uniqueItems: !0,
    items: Oe(s.valueType._def, {
      ...e,
      currentPath: [...e.currentPath, "items"]
    })
  };
  return s.minSize && Ce(a, "minItems", s.minSize.value, s.minSize.message, e), s.maxSize && Ce(a, "maxItems", s.maxSize.value, s.maxSize.message, e), a;
}
function wh(s, e) {
  return s.rest ? {
    type: "array",
    minItems: s.items.length,
    items: s.items.map((r, a) => Oe(r._def, {
      ...e,
      currentPath: [...e.currentPath, "items", `${a}`]
    })).reduce((r, a) => a === void 0 ? r : [...r, a], []),
    additionalItems: Oe(s.rest._def, {
      ...e,
      currentPath: [...e.currentPath, "additionalItems"]
    })
  } : {
    type: "array",
    minItems: s.items.length,
    maxItems: s.items.length,
    items: s.items.map((r, a) => Oe(r._def, {
      ...e,
      currentPath: [...e.currentPath, "items", `${a}`]
    })).reduce((r, a) => a === void 0 ? r : [...r, a], [])
  };
}
function xh(s) {
  return {
    not: dt(s)
  };
}
function Eh(s) {
  return dt(s);
}
const Rh = (s, e) => Oe(s.innerType._def, e), kh = (s, e, r) => {
  switch (e) {
    case U.ZodString:
      return jo(s, r);
    case U.ZodNumber:
      return mh(s, r);
    case U.ZodObject:
      return vh(s, r);
    case U.ZodBigInt:
      return Gd(s, r);
    case U.ZodBoolean:
      return Yd();
    case U.ZodDate:
      return Do(s, r);
    case U.ZodUndefined:
      return xh(r);
    case U.ZodNull:
      return hh(r);
    case U.ZodArray:
      return Wd(s, r);
    case U.ZodUnion:
    case U.ZodDiscriminatedUnion:
      return fh(s, r);
    case U.ZodIntersection:
      return nh(s, r);
    case U.ZodTuple:
      return wh(s, r);
    case U.ZodRecord:
      return Fo(s, r);
    case U.ZodLiteral:
      return ih(s, r);
    case U.ZodEnum:
      return ah(s);
    case U.ZodNativeEnum:
      return ch(s);
    case U.ZodNullable:
      return ph(s, r);
    case U.ZodOptional:
      return _h(s, r);
    case U.ZodMap:
      return lh(s, r);
    case U.ZodSet:
      return Sh(s, r);
    case U.ZodLazy:
      return () => s.getter()._def;
    case U.ZodPromise:
      return Ph(s, r);
    case U.ZodNaN:
    case U.ZodNever:
      return dh(r);
    case U.ZodEffects:
      return rh(s, r);
    case U.ZodAny:
      return dt(r);
    case U.ZodUnknown:
      return Eh(r);
    case U.ZodDefault:
      return th(s, r);
    case U.ZodBranded:
      return No(s, r);
    case U.ZodReadonly:
      return Rh(s, r);
    case U.ZodCatch:
      return Xd(s, r);
    case U.ZodPipeline:
      return bh(s, r);
    case U.ZodFunction:
    case U.ZodVoid:
    case U.ZodSymbol:
      return;
    default:
      return /* @__PURE__ */ ((a) => {
      })();
  }
};
function Oe(s, e, r = !1) {
  var o;
  const a = e.seen.get(s);
  if (e.override) {
    const u = (o = e.override) == null ? void 0 : o.call(e, s, e, a, r);
    if (u !== Qd)
      return u;
  }
  if (a && !r) {
    const u = Th(a, e);
    if (u !== void 0)
      return u;
  }
  const t = { def: s, path: e.currentPath, jsonSchema: void 0 };
  e.seen.set(s, t);
  const n = kh(s, s.typeName, e), i = typeof n == "function" ? Oe(n(), e) : n;
  if (i && Ah(s, e, i), e.postProcess) {
    const u = e.postProcess(i, s, e);
    return t.jsonSchema = i, u;
  }
  return t.jsonSchema = i, i;
}
const Th = (s, e) => {
  switch (e.$refStrategy) {
    case "root":
      return { $ref: s.path.join("/") };
    case "relative":
      return { $ref: Io(e.currentPath, s.path) };
    case "none":
    case "seen":
      return s.path.length < e.currentPath.length && s.path.every((r, a) => e.currentPath[a] === r) ? (console.warn(`Recursive reference detected at ${e.currentPath.join("/")}! Defaulting to any`), dt(e)) : e.$refStrategy === "seen" ? dt(e) : void 0;
  }
}, Ah = (s, e, r) => (s.description && (r.description = s.description, e.markdownDescription && (r.markdownDescription = s.description)), r), Zi = (s, e) => {
  const r = Jd(e);
  let a = typeof e == "object" && e.definitions ? Object.entries(e.definitions).reduce((u, [l, h]) => ({
    ...u,
    [l]: Oe(h._def, {
      ...r,
      currentPath: [...r.basePath, r.definitionPath, l]
    }, !0) ?? dt(r)
  }), {}) : void 0;
  const t = typeof e == "string" ? e : (e == null ? void 0 : e.nameStrategy) === "title" || e == null ? void 0 : e.name, n = Oe(s._def, t === void 0 ? r : {
    ...r,
    currentPath: [...r.basePath, r.definitionPath, t]
  }, !1) ?? dt(r), i = typeof e == "object" && e.name !== void 0 && e.nameStrategy === "title" ? e.name : void 0;
  i !== void 0 && (n.title = i), r.flags.hasReferencedOpenAiAnyType && (a || (a = {}), a[r.openAiAnyTypeName] || (a[r.openAiAnyTypeName] = {
    // Skipping "object" as no properties can be defined and additionalProperties must be "false"
    type: ["string", "number", "integer", "boolean", "array", "null"],
    items: {
      $ref: r.$refStrategy === "relative" ? "1" : [
        ...r.basePath,
        r.definitionPath,
        r.openAiAnyTypeName
      ].join("/")
    }
  }));
  const o = t === void 0 ? a ? {
    ...n,
    [r.definitionPath]: a
  } : n : {
    $ref: [
      ...r.$refStrategy === "relative" ? [] : r.basePath,
      r.definitionPath,
      t
    ].join("/"),
    [r.definitionPath]: {
      ...a,
      [t]: n
    }
  };
  return r.target === "jsonSchema7" ? o.$schema = "http://json-schema.org/draft-07/schema#" : (r.target === "jsonSchema2019-09" || r.target === "openAi") && (o.$schema = "https://json-schema.org/draft/2019-09/schema#"), r.target === "openAi" && ("anyOf" in o || "oneOf" in o || "allOf" in o || "type" in o && Array.isArray(o.type)) && console.warn("Warning: OpenAI may not support schemas with unions as roots! Try wrapping it in an object property."), o;
};
var Ls;
(function(s) {
  s.Completable = "McpCompletable";
})(Ls || (Ls = {}));
class Zs extends Pe {
  _parse(e) {
    const { ctx: r } = this._processInputParams(e), a = r.data;
    return this._def.type._parse({
      data: a,
      path: r.path,
      parent: r
    });
  }
  unwrap() {
    return this._def.type;
  }
}
Zs.create = (s, e) => new Zs({
  type: s,
  typeName: Ls.Completable,
  complete: e.complete,
  ...Oh(e)
});
function Oh(s) {
  if (!s)
    return {};
  const { errorMap: e, invalid_type_error: r, required_error: a, description: t } = s;
  if (e && (r || a))
    throw new Error(`Can't use "invalid_type_error" or "required_error" in conjunction with custom error map.`);
  return e ? { errorMap: e, description: t } : { errorMap: (i, o) => {
    var u, l;
    const { message: h } = s;
    return i.code === "invalid_enum_value" ? { message: h ?? o.defaultError } : typeof o.data > "u" ? { message: (u = h ?? a) !== null && u !== void 0 ? u : o.defaultError } : i.code !== "invalid_type" ? { message: o.defaultError } : { message: (l = h ?? r) !== null && l !== void 0 ? l : o.defaultError };
  }, description: t };
}
class Ch {
  constructor(e, r) {
    this._registeredResources = {}, this._registeredResourceTemplates = {}, this._registeredTools = {}, this._registeredPrompts = {}, this._toolHandlersInitialized = !1, this._completionHandlerInitialized = !1, this._resourceHandlersInitialized = !1, this._promptHandlersInitialized = !1, this.server = new Bd(e, r);
  }
  /**
   * Attaches to the given transport, starts it, and starts listening for messages.
   *
   * The `server` object assumes ownership of the Transport, replacing any callbacks that have already been set, and expects that it is the only user of the Transport instance going forward.
   */
  async connect(e) {
    return await this.server.connect(e);
  }
  /**
   * Closes the connection.
   */
  async close() {
    await this.server.close();
  }
  setToolRequestHandlers() {
    this._toolHandlersInitialized || (this.server.assertCanSetRequestHandler(js.shape.method.value), this.server.assertCanSetRequestHandler(Fs.shape.method.value), this.server.registerCapabilities({
      tools: {
        listChanged: !0
      }
    }), this.server.setRequestHandler(js, () => ({
      tools: Object.entries(this._registeredTools).filter(([, e]) => e.enabled).map(([e, r]) => {
        const a = {
          name: e,
          title: r.title,
          description: r.description,
          inputSchema: r.inputSchema ? Zi(r.inputSchema, {
            strictUnions: !0
          }) : $h,
          annotations: r.annotations
        };
        return r.outputSchema && (a.outputSchema = Zi(r.outputSchema, { strictUnions: !0 })), a;
      })
    })), this.server.setRequestHandler(Fs, async (e, r) => {
      const a = this._registeredTools[e.params.name];
      if (!a)
        throw new Ve(qe.InvalidParams, `Tool ${e.params.name} not found`);
      if (!a.enabled)
        throw new Ve(qe.InvalidParams, `Tool ${e.params.name} disabled`);
      let t;
      if (a.inputSchema) {
        const n = await a.inputSchema.safeParseAsync(e.params.arguments);
        if (!n.success)
          throw new Ve(qe.InvalidParams, `Invalid arguments for tool ${e.params.name}: ${n.error.message}`);
        const i = n.data, o = a.callback;
        try {
          t = await Promise.resolve(o(i, r));
        } catch (u) {
          t = {
            content: [
              {
                type: "text",
                text: u instanceof Error ? u.message : String(u)
              }
            ],
            isError: !0
          };
        }
      } else {
        const n = a.callback;
        try {
          t = await Promise.resolve(n(r));
        } catch (i) {
          t = {
            content: [
              {
                type: "text",
                text: i instanceof Error ? i.message : String(i)
              }
            ],
            isError: !0
          };
        }
      }
      if (a.outputSchema && !t.isError) {
        if (!t.structuredContent)
          throw new Ve(qe.InvalidParams, `Tool ${e.params.name} has an output schema but no structured content was provided`);
        const n = await a.outputSchema.safeParseAsync(t.structuredContent);
        if (!n.success)
          throw new Ve(qe.InvalidParams, `Invalid structured content for tool ${e.params.name}: ${n.error.message}`);
      }
      return t;
    }), this._toolHandlersInitialized = !0);
  }
  setCompletionRequestHandler() {
    this._completionHandlerInitialized || (this.server.assertCanSetRequestHandler(qs.shape.method.value), this.server.registerCapabilities({
      completions: {}
    }), this.server.setRequestHandler(qs, async (e) => {
      switch (e.params.ref.type) {
        case "ref/prompt":
          return this.handlePromptCompletion(e, e.params.ref);
        case "ref/resource":
          return this.handleResourceCompletion(e, e.params.ref);
        default:
          throw new Ve(qe.InvalidParams, `Invalid completion reference: ${e.params.ref}`);
      }
    }), this._completionHandlerInitialized = !0);
  }
  async handlePromptCompletion(e, r) {
    const a = this._registeredPrompts[r.name];
    if (!a)
      throw new Ve(qe.InvalidParams, `Prompt ${r.name} not found`);
    if (!a.enabled)
      throw new Ve(qe.InvalidParams, `Prompt ${r.name} disabled`);
    if (!a.argsSchema)
      return Mr;
    const t = a.argsSchema.shape[e.params.argument.name];
    if (!(t instanceof Zs))
      return Mr;
    const i = await t._def.complete(e.params.argument.value, e.params.context);
    return zi(i);
  }
  async handleResourceCompletion(e, r) {
    const a = Object.values(this._registeredResourceTemplates).find((i) => i.resourceTemplate.uriTemplate.toString() === r.uri);
    if (!a) {
      if (this._registeredResources[r.uri])
        return Mr;
      throw new Ve(qe.InvalidParams, `Resource template ${e.params.ref.uri} not found`);
    }
    const t = a.resourceTemplate.completeCallback(e.params.argument.name);
    if (!t)
      return Mr;
    const n = await t(e.params.argument.value, e.params.context);
    return zi(n);
  }
  setResourceRequestHandlers() {
    this._resourceHandlersInitialized || (this.server.assertCanSetRequestHandler(Cs.shape.method.value), this.server.assertCanSetRequestHandler($s.shape.method.value), this.server.assertCanSetRequestHandler(Is.shape.method.value), this.server.registerCapabilities({
      resources: {
        listChanged: !0
      }
    }), this.server.setRequestHandler(Cs, async (e, r) => {
      const a = Object.entries(this._registeredResources).filter(([n, i]) => i.enabled).map(([n, i]) => ({
        uri: n,
        name: i.name,
        ...i.metadata
      })), t = [];
      for (const n of Object.values(this._registeredResourceTemplates)) {
        if (!n.resourceTemplate.listCallback)
          continue;
        const i = await n.resourceTemplate.listCallback(r);
        for (const o of i.resources)
          t.push({
            ...n.metadata,
            // the defined resource metadata should override the template metadata if present
            ...o
          });
      }
      return { resources: [...a, ...t] };
    }), this.server.setRequestHandler($s, async () => ({ resourceTemplates: Object.entries(this._registeredResourceTemplates).map(([r, a]) => ({
      name: r,
      uriTemplate: a.resourceTemplate.uriTemplate.toString(),
      ...a.metadata
    })) })), this.server.setRequestHandler(Is, async (e, r) => {
      const a = new URL(e.params.uri), t = this._registeredResources[a.toString()];
      if (t) {
        if (!t.enabled)
          throw new Ve(qe.InvalidParams, `Resource ${a} disabled`);
        return t.readCallback(a, r);
      }
      for (const n of Object.values(this._registeredResourceTemplates)) {
        const i = n.resourceTemplate.uriTemplate.match(a.toString());
        if (i)
          return n.readCallback(a, i, r);
      }
      throw new Ve(qe.InvalidParams, `Resource ${a} not found`);
    }), this.setCompletionRequestHandler(), this._resourceHandlersInitialized = !0);
  }
  setPromptRequestHandlers() {
    this._promptHandlersInitialized || (this.server.assertCanSetRequestHandler(Ns.shape.method.value), this.server.assertCanSetRequestHandler(Ds.shape.method.value), this.server.registerCapabilities({
      prompts: {
        listChanged: !0
      }
    }), this.server.setRequestHandler(Ns, () => ({
      prompts: Object.entries(this._registeredPrompts).filter(([, e]) => e.enabled).map(([e, r]) => ({
        name: e,
        title: r.title,
        description: r.description,
        arguments: r.argsSchema ? Nh(r.argsSchema) : void 0
      }))
    })), this.server.setRequestHandler(Ds, async (e, r) => {
      const a = this._registeredPrompts[e.params.name];
      if (!a)
        throw new Ve(qe.InvalidParams, `Prompt ${e.params.name} not found`);
      if (!a.enabled)
        throw new Ve(qe.InvalidParams, `Prompt ${e.params.name} disabled`);
      if (a.argsSchema) {
        const t = await a.argsSchema.safeParseAsync(e.params.arguments);
        if (!t.success)
          throw new Ve(qe.InvalidParams, `Invalid arguments for prompt ${e.params.name}: ${t.error.message}`);
        const n = t.data, i = a.callback;
        return await Promise.resolve(i(n, r));
      } else {
        const t = a.callback;
        return await Promise.resolve(t(r));
      }
    }), this.setCompletionRequestHandler(), this._promptHandlersInitialized = !0);
  }
  resource(e, r, ...a) {
    let t;
    typeof a[0] == "object" && (t = a.shift());
    const n = a[0];
    if (typeof r == "string") {
      if (this._registeredResources[r])
        throw new Error(`Resource ${r} is already registered`);
      const i = this._createRegisteredResource(e, void 0, r, t, n);
      return this.setResourceRequestHandlers(), this.sendResourceListChanged(), i;
    } else {
      if (this._registeredResourceTemplates[e])
        throw new Error(`Resource template ${e} is already registered`);
      const i = this._createRegisteredResourceTemplate(e, void 0, r, t, n);
      return this.setResourceRequestHandlers(), this.sendResourceListChanged(), i;
    }
  }
  registerResource(e, r, a, t) {
    if (typeof r == "string") {
      if (this._registeredResources[r])
        throw new Error(`Resource ${r} is already registered`);
      const n = this._createRegisteredResource(e, a.title, r, a, t);
      return this.setResourceRequestHandlers(), this.sendResourceListChanged(), n;
    } else {
      if (this._registeredResourceTemplates[e])
        throw new Error(`Resource template ${e} is already registered`);
      const n = this._createRegisteredResourceTemplate(e, a.title, r, a, t);
      return this.setResourceRequestHandlers(), this.sendResourceListChanged(), n;
    }
  }
  _createRegisteredResource(e, r, a, t, n) {
    const i = {
      name: e,
      title: r,
      metadata: t,
      readCallback: n,
      enabled: !0,
      disable: () => i.update({ enabled: !1 }),
      enable: () => i.update({ enabled: !0 }),
      remove: () => i.update({ uri: null }),
      update: (o) => {
        typeof o.uri < "u" && o.uri !== a && (delete this._registeredResources[a], o.uri && (this._registeredResources[o.uri] = i)), typeof o.name < "u" && (i.name = o.name), typeof o.title < "u" && (i.title = o.title), typeof o.metadata < "u" && (i.metadata = o.metadata), typeof o.callback < "u" && (i.readCallback = o.callback), typeof o.enabled < "u" && (i.enabled = o.enabled), this.sendResourceListChanged();
      }
    };
    return this._registeredResources[a] = i, i;
  }
  _createRegisteredResourceTemplate(e, r, a, t, n) {
    const i = {
      resourceTemplate: a,
      title: r,
      metadata: t,
      readCallback: n,
      enabled: !0,
      disable: () => i.update({ enabled: !1 }),
      enable: () => i.update({ enabled: !0 }),
      remove: () => i.update({ name: null }),
      update: (o) => {
        typeof o.name < "u" && o.name !== e && (delete this._registeredResourceTemplates[e], o.name && (this._registeredResourceTemplates[o.name] = i)), typeof o.title < "u" && (i.title = o.title), typeof o.template < "u" && (i.resourceTemplate = o.template), typeof o.metadata < "u" && (i.metadata = o.metadata), typeof o.callback < "u" && (i.readCallback = o.callback), typeof o.enabled < "u" && (i.enabled = o.enabled), this.sendResourceListChanged();
      }
    };
    return this._registeredResourceTemplates[e] = i, i;
  }
  _createRegisteredPrompt(e, r, a, t, n) {
    const i = {
      title: r,
      description: a,
      argsSchema: t === void 0 ? void 0 : W(t),
      callback: n,
      enabled: !0,
      disable: () => i.update({ enabled: !1 }),
      enable: () => i.update({ enabled: !0 }),
      remove: () => i.update({ name: null }),
      update: (o) => {
        typeof o.name < "u" && o.name !== e && (delete this._registeredPrompts[e], o.name && (this._registeredPrompts[o.name] = i)), typeof o.title < "u" && (i.title = o.title), typeof o.description < "u" && (i.description = o.description), typeof o.argsSchema < "u" && (i.argsSchema = W(o.argsSchema)), typeof o.callback < "u" && (i.callback = o.callback), typeof o.enabled < "u" && (i.enabled = o.enabled), this.sendPromptListChanged();
      }
    };
    return this._registeredPrompts[e] = i, i;
  }
  _createRegisteredTool(e, r, a, t, n, i, o) {
    const u = {
      title: r,
      description: a,
      inputSchema: t === void 0 ? void 0 : W(t),
      outputSchema: n === void 0 ? void 0 : W(n),
      annotations: i,
      callback: o,
      enabled: !0,
      disable: () => u.update({ enabled: !1 }),
      enable: () => u.update({ enabled: !0 }),
      remove: () => u.update({ name: null }),
      update: (l) => {
        typeof l.name < "u" && l.name !== e && (delete this._registeredTools[e], l.name && (this._registeredTools[l.name] = u)), typeof l.title < "u" && (u.title = l.title), typeof l.description < "u" && (u.description = l.description), typeof l.paramsSchema < "u" && (u.inputSchema = W(l.paramsSchema)), typeof l.callback < "u" && (u.callback = l.callback), typeof l.annotations < "u" && (u.annotations = l.annotations), typeof l.enabled < "u" && (u.enabled = l.enabled), this.sendToolListChanged();
      }
    };
    return this._registeredTools[e] = u, this.setToolRequestHandlers(), this.sendToolListChanged(), u;
  }
  /**
   * tool() implementation. Parses arguments passed to overrides defined above.
   */
  tool(e, ...r) {
    if (this._registeredTools[e])
      throw new Error(`Tool ${e} is already registered`);
    let a, t, n, i;
    if (typeof r[0] == "string" && (a = r.shift()), r.length > 1) {
      const u = r[0];
      Mi(u) ? (t = r.shift(), r.length > 1 && typeof r[0] == "object" && r[0] !== null && !Mi(r[0]) && (i = r.shift())) : typeof u == "object" && u !== null && (i = r.shift());
    }
    const o = r[0];
    return this._createRegisteredTool(e, void 0, a, t, n, i, o);
  }
  /**
   * Registers a tool with a config object and callback.
   */
  registerTool(e, r, a) {
    if (this._registeredTools[e])
      throw new Error(`Tool ${e} is already registered`);
    const { title: t, description: n, inputSchema: i, outputSchema: o, annotations: u } = r;
    return this._createRegisteredTool(e, t, n, i, o, u, a);
  }
  prompt(e, ...r) {
    if (this._registeredPrompts[e])
      throw new Error(`Prompt ${e} is already registered`);
    let a;
    typeof r[0] == "string" && (a = r.shift());
    let t;
    r.length > 1 && (t = r.shift());
    const n = r[0], i = this._createRegisteredPrompt(e, void 0, a, t, n);
    return this.setPromptRequestHandlers(), this.sendPromptListChanged(), i;
  }
  /**
   * Registers a prompt with a config object and callback.
   */
  registerPrompt(e, r, a) {
    if (this._registeredPrompts[e])
      throw new Error(`Prompt ${e} is already registered`);
    const { title: t, description: n, argsSchema: i } = r, o = this._createRegisteredPrompt(e, t, n, i, a);
    return this.setPromptRequestHandlers(), this.sendPromptListChanged(), o;
  }
  /**
   * Checks if the server is connected to a transport.
   * @returns True if the server is connected
   */
  isConnected() {
    return this.server.transport !== void 0;
  }
  /**
   * Sends a resource list changed event to the client, if connected.
   */
  sendResourceListChanged() {
    this.isConnected() && this.server.sendResourceListChanged();
  }
  /**
   * Sends a tool list changed event to the client, if connected.
   */
  sendToolListChanged() {
    this.isConnected() && this.server.sendToolListChanged();
  }
  /**
   * Sends a prompt list changed event to the client, if connected.
   */
  sendPromptListChanged() {
    this.isConnected() && this.server.sendPromptListChanged();
  }
}
const $h = {
  type: "object",
  properties: {}
};
function Mi(s) {
  return typeof s != "object" || s === null ? !1 : Object.keys(s).length === 0 || Object.values(s).some(Ih);
}
function Ih(s) {
  return s !== null && typeof s == "object" && "parse" in s && typeof s.parse == "function" && "safeParse" in s && typeof s.safeParse == "function";
}
function Nh(s) {
  return Object.entries(s.shape).map(([e, r]) => ({
    name: e,
    description: r.description,
    required: !r.isOptional()
  }));
}
function zi(s) {
  return {
    completion: {
      values: s.slice(0, 100),
      total: s.length,
      hasMore: s.length > 100
    }
  };
}
const Mr = {
  completion: {
    values: [],
    hasMore: !1
  }
};
async function Dh(s, e) {
  var a, t, n;
  if (!((a = window.jetEngineCompatibilityAngie) != null && a.api_base))
    throw new Error("API base URL is not defined");
  const r = await fetch(
    ((t = window.jetEngineCompatibilityAngie) == null ? void 0 : t.api_base) + s,
    {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-WP-Nonce": ((n = window.jetEngineCompatibilityAngie) == null ? void 0 : n.nonce) || ""
      },
      body: JSON.stringify({ input: e })
    }
  );
  if (!r.ok)
    throw new Error(`HTTP error! status: ${r.status}`);
  return await r.json();
}
function qo(s) {
  const e = {};
  for (const r in s)
    e[r] = Lo(s[r]);
  return e;
}
const Lo = (s) => {
  let e;
  switch (s.type) {
    case "string":
      s.enum ? e = Nt(s.enum) : e = H();
      break;
    case "number":
      e = et();
      break;
    case "boolean":
      e = ct();
      break;
    case "array":
      e = Ke(Lo(s.items));
      break;
    case "object":
      s.properties ? e = W(qo(s.properties)) : e = gr(H(), Gl());
      break;
    default:
      e = H();
  }
  return s.description && (e = e.describe(s.description)), e;
};
function jh() {
  var e;
  const s = new Ch(
    {
      name: "croco-angie-mcp-server",
      version: "1.0.0"
    },
    {
      capabilities: {
        tools: {}
      }
    }
  );
  for (const r of ((e = window.jetEngineCompatibilityAngie) == null ? void 0 : e.features) || []) {
    let a = r.id;
    a = a.replace(/\//g, "-"), a = a.replace(/-/g, "_"), s.registerTool(
      a,
      {
        title: r.label,
        description: r.description,
        inputSchema: qo(r.input_schema.properties)
      },
      async (t, n) => {
        const i = await Dh(r.name, t);
        return console.log(n), {
          content: [{
            type: "text",
            text: JSON.stringify(i, null, 2)
          }]
        };
      }
    );
  }
  return s;
}
const Fh = async () => {
  try {
    const s = jh();
    await new El().registerServer({
      name: "croco-angie-mcp-server",
      version: "1.0.0",
      description: "Crocoblock MCP Server for Angie AI assistant",
      server: s
    }), console.log("Crocoblock MCP Server registered with Angie successfully");
  } catch (s) {
    console.error("Failed to register Crocoblock MCP Server with Angie:", s);
  }
};
Fh();
